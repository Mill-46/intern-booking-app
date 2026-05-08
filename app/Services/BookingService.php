<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\BookingStatusUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {}

    /**
     * @param array<string, mixed> $validated
     */
    public function hasOverlappingBooking(array $validated, ?int $ignoreBookingId = null): bool
    {
        return Booking::query()
            ->where('vehicle_id', (int) $validated['vehicle_id'])
            ->when($ignoreBookingId !== null, function ($query) use ($ignoreBookingId) {
                $query->whereKeyNot($ignoreBookingId);
            })
            ->whereIn('status', [
                Booking::STATUS_SUBMITTED,
                Booking::STATUS_APPROVED_L1,
                Booking::STATUS_APPROVED_L2,
                Booking::STATUS_CONFIRMED,
            ])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_at', [$validated['start_at'], $validated['end_at']])
                    ->orWhereBetween('end_at', [$validated['start_at'], $validated['end_at']])
                    ->orWhere(function ($inner) use ($validated) {
                        $inner->where('start_at', '<=', $validated['start_at'])
                            ->where('end_at', '>=', $validated['end_at']);
                    });
            })
            ->exists();
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function createDraft(array $validated, Request $request): Booking
    {
        $destinationName = Site::query()->find($validated['destination_site_id'])?->name ?? $validated['destination'];

        $booking = Booking::create([
            ...$validated,
            'destination' => $destinationName,
            'user_id' => Auth::id(),
            'status' => Booking::STATUS_DRAFT,
        ]);

        $this->activityLogService->log(
            $request,
            'create_booking',
            'Created booking #' . $booking->id,
            null,
            [
                'booking_id' => $booking->id,
                'to_status' => $booking->status,
            ],
        );

        return $booking;
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function updateBooking(Booking $booking, array $validated, Request $request): Booking
    {
        $destinationName = Site::query()->find($validated['destination_site_id'])?->name ?? $validated['destination'];
        $validated['destination'] = $destinationName;

        $booking->update($validated);

        $this->activityLogService->log(
            $request,
            'update_booking',
            'Updated booking #' . $booking->id,
            null,
            [
                'booking_id' => $booking->id,
                'status' => $booking->status,
            ],
        );

        return $booking;
    }

    /**
     * Submit booking for approval (draft -> submitted) and create approvals (level 1 & 2).
     */
    public function submit(Booking $booking, Request $request): Booking
    {
        $this->authorizeApproversExist($booking);

        $previousStatus = $booking->status;

        $approverL1 = User::whereKey($booking->approver_l1_id)
            ->where('role', User::ROLE_APPROVER_L1)
            ->first();

        $approverL2 = User::whereKey($booking->approver_l2_id)
            ->where('role', User::ROLE_APPROVER_L2)
            ->first();

        if (! $approverL1 || ! $approverL2) {
            throw ValidationException::withMessages([
                'approver_l1_id' => 'Silakan tentukan approver level 1 dan level 2 yang valid sebelum submit.',
            ]);
        }

        DB::transaction(function () use ($booking, $approverL1, $approverL2): void {
            $booking->update(['status' => Booking::STATUS_SUBMITTED]);

            Approval::create([
                'booking_id' => $booking->id,
                'approver_id' => $approverL1->id,
                'level' => 1,
                'status' => 'pending',
            ]);

            Approval::create([
                'booking_id' => $booking->id,
                'approver_id' => $approverL2->id,
                'level' => 2,
                'status' => 'pending',
            ]);

            // Both approvers get notified with the same message (matches existing behavior).
            // Booking model is not loaded here; existing controller just calls notify using $booking.
            // The notification itself uses the booking instance.
            $approverL1->notify(new BookingStatusUpdatedNotification(
                $booking,
                'Pemesanan telah diajukan dan menunggu persetujuan Anda.'
            ));
            $approverL2->notify(new BookingStatusUpdatedNotification(
                $booking,
                'Pemesanan telah diajukan dan menunggu persetujuan Anda.'
            ));
        });

        $this->activityLogService->log(
            $request,
            'submit_booking',
            'Submitted booking #' . $booking->id,
            null,
            [
                'booking_id' => $booking->id,
                'from_status' => $previousStatus,
                'to_status' => Booking::STATUS_SUBMITTED,
                'approver_l1_id' => $approverL1->id,
                'approver_l2_id' => $approverL2->id,
            ],
        );

        return $booking;
    }

    public function confirm(Booking $booking, Request $request): Booking
    {
        $previousStatus = $booking->status;
        $booking->update(['status' => Booking::STATUS_CONFIRMED]);

        $this->activityLogService->log(
            $request,
            'confirm_booking',
            'Confirmed booking #' . $booking->id,
            null,
            [
                'booking_id' => $booking->id,
                'from_status' => $previousStatus,
                'to_status' => Booking::STATUS_CONFIRMED,
            ],
        );

        $booking->user->notify(new BookingStatusUpdatedNotification(
            $booking,
            'Pemesanan telah dikonfirmasi dan siap dieksekusi.'
        ));

        return $booking;
    }

    public function complete(Booking $booking, Request $request): Booking
    {
        $previousStatus = $booking->status;
        $booking->update(['status' => Booking::STATUS_COMPLETED]);

        $this->activityLogService->log(
            $request,
            'complete_booking',
            'Completed booking #' . $booking->id,
            null,
            [
                'booking_id' => $booking->id,
                'from_status' => $previousStatus,
                'to_status' => Booking::STATUS_COMPLETED,
            ],
        );

        $booking->user->notify(new BookingStatusUpdatedNotification(
            $booking,
            'Pemesanan telah selesai.'
        ));

        return $booking;
    }

    private function authorizeApproversExist(Booking $booking): void
    {
        // Authorization is handled by BookingPolicy in the controller (authorize('submit', $booking)).
        // Here we only validate DB consistency like existing controller did.
        if ($booking->approver_l1_id === null || $booking->approver_l2_id === null) {
            throw ValidationException::withMessages([
                'approver_l1_id' => 'Silakan tentukan approver level 1 dan level 2 yang valid sebelum submit.',
            ]);
        }
    }
}
