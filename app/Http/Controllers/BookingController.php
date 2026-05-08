<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingReportFilterRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    public function index(BookingReportFilterRequest $request): View
    {
        $this->authorize('viewAny', Booking::class);

        $filters = $request->validated();
        $preset = $request->string('preset')->toString();

        if ($preset === 'today') {
            $filters['from'] = now()->toDateString();
            $filters['to'] = now()->toDateString();
        }

        if ($preset === 'this_month') {
            $filters['from'] = now()->startOfMonth()->toDateString();
            $filters['to'] = now()->endOfMonth()->toDateString();
        }

        $bookings = Booking::with(['user', 'vehicle', 'driver', 'originSite', 'destinationSite', 'approverL1', 'approverL2'])
            ->when(($filters['q'] ?? '') !== '', function ($query) use ($filters) {
                $keyword = trim((string) $filters['q']);

                $query->where(function ($nestedQuery) use ($keyword) {
                    $nestedQuery->where('destination', 'like', '%' . $keyword . '%')
                        ->orWhere('purpose', 'like', '%' . $keyword . '%')
                        ->orWhereHas('vehicle', function ($vehicleQuery) use ($keyword) {
                            $vehicleQuery->where('registration_no', 'like', '%' . $keyword . '%');
                        })
                        ->orWhereHas('driver', function ($driverQuery) use ($keyword) {
                            $driverQuery->where('name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhereHas('user', function ($userQuery) use ($keyword) {
                            $userQuery->where('name', 'like', '%' . $keyword . '%');
                        });
                });
            })
            ->when(($filters['status'] ?? '') !== '', function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(((int) ($filters['vehicle_id'] ?? 0)) > 0, function ($query) use ($filters) {
                $query->where('vehicle_id', (int) $filters['vehicle_id']);
            })
            ->when(((int) ($filters['driver_id'] ?? 0)) > 0, function ($query) use ($filters) {
                $query->where('driver_id', (int) $filters['driver_id']);
            })
            ->when(((int) ($filters['requester_id'] ?? 0)) > 0, function ($query) use ($filters) {
                $query->where('user_id', (int) $filters['requester_id']);
            })
            ->when(((int) ($filters['origin_site_id'] ?? 0)) > 0, function ($query) use ($filters) {
                $query->where('origin_site_id', (int) $filters['origin_site_id']);
            })
            ->when(((int) ($filters['destination_site_id'] ?? 0)) > 0, function ($query) use ($filters) {
                $query->where('destination_site_id', (int) $filters['destination_site_id']);
            })
            ->when(($filters['from'] ?? '') !== '', function ($query) use ($filters) {
                $query->whereDate('start_at', '>=', $filters['from']);
            })
            ->when(($filters['to'] ?? '') !== '', function ($query) use ($filters) {
                $query->whereDate('end_at', '<=', $filters['to']);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('bookings.index', [
            'bookings' => $bookings,
            'vehicles' => Vehicle::orderBy('registration_no')->get(),
            'drivers' => Driver::orderBy('name')->get(),
            'requesters' => User::orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Booking::class);

        return view('bookings.create', [
            'vehicles' => Vehicle::where('status', 'available')->orderBy('registration_no')->get(),
            'drivers' => Driver::where('status', 'active')->orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
            'approversL1' => User::where('role', User::ROLE_APPROVER_L1)->orderBy('name')->get(),
            'approversL2' => User::where('role', User::ROLE_APPROVER_L2)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $this->authorize('create', Booking::class);

        $validated = $request->validated();

        if ($this->bookingService->hasOverlappingBooking($validated)) {
            return back()->withErrors(['vehicle_id' => 'Kendaraan sudah digunakan pada rentang waktu tersebut.'])->withInput();
        }

        $booking = $this->bookingService->createDraft($validated, $request);

        return redirect()->route('bookings.index')->with('status', 'Pemesanan berhasil dibuat.');
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['user', 'vehicle', 'driver', 'originSite', 'destinationSite', 'approverL1', 'approverL2', 'approvals.approver']);

        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking): View
    {
        $this->authorize('update', $booking);

        return view('bookings.edit', [
            'booking' => $booking,
            'vehicles' => Vehicle::where('status', 'available')->orderBy('registration_no')->get(),
            'drivers' => Driver::where('status', 'active')->orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
            'approversL1' => User::where('role', User::ROLE_APPROVER_L1)->orderBy('name')->get(),
            'approversL2' => User::where('role', User::ROLE_APPROVER_L2)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);

        $validated = $request->validated();

        if ($this->bookingService->hasOverlappingBooking($validated, $booking->id)) {
            return back()->withErrors(['vehicle_id' => 'Kendaraan sudah digunakan pada rentang waktu tersebut.'])->withInput();
        }

        $this->bookingService->updateBooking($booking, $validated, $request);

        return redirect()->route('bookings.index')->with('status', 'Pemesanan berhasil diperbarui.');
    }

    public function destroy(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('delete', $booking);

        $booking->delete();

        $this->logActivity($request, 'delete_booking', 'Deleted booking #' . $booking->id, null, [
            'booking_id' => $booking->id,
            'status' => $booking->status,
        ]);

        return redirect()->route('bookings.index')->with('status', 'Pemesanan berhasil dihapus.');
    }

    public function submit(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('submit', $booking);

        $validatedRequest = $request; // keep signature; BookingService expects Request for ActivityLog + notifications

        try {
            $this->bookingService->submit($booking, $validatedRequest);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors([
                'approver_l1_id' => $e->errors()['approver_l1_id'][0] ?? 'Silakan tentukan approver level 1 dan level 2 yang valid sebelum submit.',
            ]);
        }

        return back()->with('status', 'Pemesanan berhasil diajukan untuk persetujuan.');
    }

    public function confirm(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('confirm', $booking);

        $this->bookingService->confirm($booking, $request);

        return back()->with('status', 'Pemesanan berhasil dikonfirmasi.');
    }

    public function complete(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('complete', $booking);

        $this->bookingService->complete($booking, $request);

        return back()->with('status', 'Pemesanan berhasil ditandai selesai.');
    }
}
