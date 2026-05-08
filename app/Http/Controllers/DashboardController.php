<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Booking;
use App\Models\FuelConsumption;
use App\Models\Vehicle;
use App\Models\VehicleService;
use Illuminate\Support\Collection;
use App\Models\VehicleUsage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $periodExpression = match (DB::getDriverName()) {
            'pgsql' => "to_char(recorded_at, 'YYYY-MM')",
            'mysql', 'mariadb' => "date_format(recorded_at, '%Y-%m')",
            default => "strftime('%Y-%m', recorded_at)",
        };

        $statusCounts = Cache::remember('dashboard.status_counts', 3600, function () {
            $counts = Booking::query()
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all();

            return collect(Booking::statusLabels())
                ->map(fn($_label, $status) => (int) ($counts[$status] ?? 0));
        });

        $fuelByMonth = Cache::remember('dashboard.fuel_by_month', 3600, function () use ($periodExpression) {
            $raw = FuelConsumption::query()
                ->selectRaw($periodExpression . ' as period, sum(fuel_used) as total_fuel')
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('total_fuel', 'period')
                ->map(fn($value): float => (float) $value)
                ->all();

            $now = now();
            return collect(range(5, 0, -1))
                ->mapWithKeys(fn($offset) => [
                    $now->copy()->subMonths($offset)->format('Y-m') => $raw[$now->copy()->subMonths($offset)->format('Y-m')] ?? 0.0,
                ])
                ->all();
        });

        $vehicleUsage = Cache::remember('dashboard.vehicle_usage', 3600, function () {
            return Booking::query()
                ->select('vehicles.registration_no', DB::raw('count(bookings.id) as total'))
                ->join('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')
                ->whereNotIn('bookings.status', [Booking::STATUS_DRAFT])
                ->groupBy('vehicles.registration_no')
                ->orderByDesc('total')
                ->limit(8)
                ->pluck('total', 'registration_no')
                ->map(fn ($value): int => (int) $value)
                ->all();
        });

        $siteUsage = Cache::remember('dashboard.site_usage', 3600, function () {
            return Booking::query()
                ->select('sites.name', DB::raw('count(bookings.id) as total'))
                ->join('sites', 'sites.id', '=', 'bookings.destination_site_id')
                ->whereNotIn('bookings.status', [Booking::STATUS_DRAFT])
                ->groupBy('sites.name')
                ->orderByDesc('total')
                ->limit(8)
                ->pluck('total', 'name')
                ->map(fn ($value): int => (int) $value)
                ->all();
        });

        $pendingApprovalsByLevel = Cache::remember('dashboard.pending_approvals_by_level', 3600, function () {
            $raw = Approval::query()
                ->select('level', DB::raw('count(*) as total'))
                ->where('status', 'pending')
                ->groupBy('level')
                ->pluck('total', 'level')
                ->all();

            return [
                1 => (int) ($raw[1] ?? 0),
                2 => (int) ($raw[2] ?? 0),
            ];
        });

        $vehicleOwnership = Cache::remember('dashboard.vehicle_ownership', 3600, function () {
            $raw = Vehicle::query()
                ->select('owner', DB::raw('count(*) as total'))
                ->groupBy('owner')
                ->pluck('total', 'owner')
                ->all();

            return [
                'company' => (int) ($raw['company'] ?? 0),
                'rental' => (int) ($raw['rental'] ?? 0),
            ];
        });

        $upcomingServices = Cache::remember('dashboard.upcoming_services', 3600, function () {
            return $this->fetchUpcomingServices();
        });

        if (! $this->isValidUpcomingServicesPayload($upcomingServices)) {
            Cache::forget('dashboard.upcoming_services');
            $upcomingServices = Cache::remember('dashboard.upcoming_services', 3600, function () {
                return $this->fetchUpcomingServices();
            });
        }

        return view('dashboard', [
            'totalBookings' => Cache::remember('dashboard.total_bookings', 3600, fn() => Booking::count()),
            'activeBookings' => Cache::remember('dashboard.active_bookings', 3600, fn() => Booking::whereIn('status', [Booking::STATUS_SUBMITTED, Booking::STATUS_APPROVED_L1])->count()),
            'approvedBookings' => Cache::remember('dashboard.approved_bookings', 3600, fn() => Booking::where('status', Booking::STATUS_APPROVED_L2)->count()),
            'totalFuelUsed' => Cache::remember('dashboard.total_fuel_used', 3600, fn() => (float) FuelConsumption::sum('fuel_used')),
            'totalDistanceKm' => Cache::remember('dashboard.total_distance_km', 3600, fn() => (int) VehicleUsage::query()->selectRaw('coalesce(sum(odometer_end - odometer_start), 0) as total')->value('total')),
            'statusCounts' => $statusCounts,
            'fuelByMonth' => $fuelByMonth,
            'vehicleUsage' => $vehicleUsage,
            'siteUsage' => $siteUsage,
            'pendingApprovalsByLevel' => $pendingApprovalsByLevel,
            'vehicleOwnership' => $vehicleOwnership,
            'upcomingServices' => $upcomingServices,
        ]);
    }

    private function fetchUpcomingServices(): Collection
    {
        return VehicleService::query()
            ->with('vehicle:id,registration_no,brand,model')
            ->whereDate('service_date', '>=', now()->toDateString())
            ->orderBy('service_date')
            ->limit(6)
            ->get();
    }

    /**
     * @param mixed $payload
     */
    private function isValidUpcomingServicesPayload(mixed $payload): bool
    {
        if (! $payload instanceof Collection) {
            return false;
        }

        return $payload->every(function ($item): bool {
            return $item instanceof VehicleService;
        });
    }
}
