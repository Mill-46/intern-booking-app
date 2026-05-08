<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Approval;
use App\Models\Booking;
use App\Models\FuelConsumption;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleUsage;
use App\Models\VehicleService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin User',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);

        User::query()->updateOrCreate(['email' => 'approver1@example.com'], [
            'name' => 'Approver L1',
            'password' => 'password123',
            'role' => User::ROLE_APPROVER_L1,
        ]);

        User::query()->updateOrCreate(['email' => 'approver2@example.com'], [
            'name' => 'Approver L2',
            'password' => 'password123',
            'role' => User::ROLE_APPROVER_L2,
        ]);

        $vehicles = [
            ['registration_no' => 'DT 1101 HQ', 'vehicle_type' => 'person', 'brand' => 'Toyota', 'model' => 'Hilux Double Cabin', 'fuel_capacity' => 80, 'mileage' => 128500, 'status' => 'available', 'owner' => 'company'],
            ['registration_no' => 'DT 1102 HQ', 'vehicle_type' => 'person', 'brand' => 'Mitsubishi', 'model' => 'Pajero Sport', 'fuel_capacity' => 68, 'mileage' => 96400, 'status' => 'available', 'owner' => 'company'],
            ['registration_no' => 'DT 2203 CB', 'vehicle_type' => 'cargo', 'brand' => 'Isuzu', 'model' => 'Traga Box', 'fuel_capacity' => 50, 'mileage' => 156300, 'status' => 'rented', 'owner' => 'rental'],
            ['registration_no' => 'B 9451 UXP', 'vehicle_type' => 'cargo', 'brand' => 'Hino', 'model' => 'Dutro 110 SD', 'fuel_capacity' => 100, 'mileage' => 211900, 'status' => 'maintenance', 'owner' => 'company'],
            ['registration_no' => 'DD 8721 KM', 'vehicle_type' => 'person', 'brand' => 'Suzuki', 'model' => 'Ertiga', 'fuel_capacity' => 45, 'mileage' => 88400, 'status' => 'available', 'owner' => 'rental'],
            ['registration_no' => 'DN 7332 TR', 'vehicle_type' => 'person', 'brand' => 'Toyota', 'model' => 'Avanza', 'fuel_capacity' => 43, 'mileage' => 120700, 'status' => 'available', 'owner' => 'company'],
            ['registration_no' => 'DB 5410 MS', 'vehicle_type' => 'cargo', 'brand' => 'Mitsubishi', 'model' => 'Colt Diesel FE 74', 'fuel_capacity' => 100, 'mileage' => 198600, 'status' => 'rented', 'owner' => 'rental'],
            ['registration_no' => 'B 1734 TMA', 'vehicle_type' => 'person', 'brand' => 'Toyota', 'model' => 'Fortuner', 'fuel_capacity' => 80, 'mileage' => 75400, 'status' => 'available', 'owner' => 'company'],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::query()->updateOrCreate(
                ['registration_no' => $vehicle['registration_no']],
                $vehicle,
            );
        }

        $drivers = [
            ['name' => 'Andi Saputra', 'phone' => '0812-4100-1201', 'license_no' => 'SIMB-7401-2022-001', 'license_expiry' => '2028-03-15', 'status' => 'active'],
            ['name' => 'Muhammad Rizal', 'phone' => '0813-5501-2202', 'license_no' => 'SIMB-7401-2021-014', 'license_expiry' => '2027-11-08', 'status' => 'active'],
            ['name' => 'Fajar Hidayat', 'phone' => '0821-9130-3303', 'license_no' => 'SIMB-7401-2020-028', 'license_expiry' => '2026-09-21', 'status' => 'active'],
            ['name' => 'Rahmat Kurniawan', 'phone' => '0812-9954-4404', 'license_no' => 'SIMB-7401-2019-035', 'license_expiry' => '2027-02-03', 'status' => 'inactive'],
            ['name' => 'Yusuf Maulana', 'phone' => '0813-7742-5505', 'license_no' => 'SIMB-7401-2023-047', 'license_expiry' => '2029-06-19', 'status' => 'active'],
            ['name' => 'Ari Wibowo', 'phone' => '0822-6883-6606', 'license_no' => 'SIMB-7401-2022-052', 'license_expiry' => '2028-12-11', 'status' => 'active'],
            ['name' => 'Dimas Pratama', 'phone' => '0812-8840-7707', 'license_no' => 'SIMB-7401-2018-066', 'license_expiry' => '2026-08-28', 'status' => 'active'],
            ['name' => 'Agus Salim', 'phone' => '0813-4602-8808', 'license_no' => 'SIMB-7401-2024-071', 'license_expiry' => '2030-01-05', 'status' => 'active'],
        ];

        foreach ($drivers as $driver) {
            Driver::query()->updateOrCreate(
                ['license_no' => $driver['license_no']],
                $driver,
            );
        }

        $sites = collect([
            ['name' => 'Kantor Pusat Jakarta', 'site_type' => Site::TYPE_HEAD_OFFICE, 'region' => 'Jakarta'],
            ['name' => 'Kantor Cabang Kendari', 'site_type' => Site::TYPE_BRANCH_OFFICE, 'region' => 'Sulawesi Tenggara'],
            ['name' => 'Tambang Morowali', 'site_type' => Site::TYPE_MINE_SITE, 'region' => 'Sulawesi Tengah'],
            ['name' => 'Tambang Konawe', 'site_type' => Site::TYPE_MINE_SITE, 'region' => 'Sulawesi Tenggara'],
            ['name' => 'Tambang Kolaka', 'site_type' => Site::TYPE_MINE_SITE, 'region' => 'Sulawesi Tenggara'],
            ['name' => 'Tambang Halmahera', 'site_type' => Site::TYPE_MINE_SITE, 'region' => 'Maluku Utara'],
            ['name' => 'Tambang Obi', 'site_type' => Site::TYPE_MINE_SITE, 'region' => 'Maluku Utara'],
            ['name' => 'Tambang Weda', 'site_type' => Site::TYPE_MINE_SITE, 'region' => 'Maluku Utara'],
        ])->mapWithKeys(function (array $site): array {
            Site::query()->updateOrCreate(['name' => $site['name']], $site);

            return [$site['name'] => Site::query()->where('name', $site['name'])->firstOrFail()];
        });

        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();
        $approverL1 = User::query()->where('role', User::ROLE_APPROVER_L1)->firstOrFail();
        $approverL2 = User::query()->where('role', User::ROLE_APPROVER_L2)->firstOrFail();

        $bookingScenarios = [
            ['site' => 'Tambang Morowali', 'days_ago' => 35, 'duration' => 9, 'status' => Booking::STATUS_COMPLETED, 'vehicle' => 'DT 1101 HQ', 'fuel' => 46.5],
            ['site' => 'Tambang Konawe', 'days_ago' => 30, 'duration' => 7, 'status' => Booking::STATUS_COMPLETED, 'vehicle' => 'DT 1102 HQ', 'fuel' => 38.2],
            ['site' => 'Tambang Kolaka', 'days_ago' => 26, 'duration' => 8, 'status' => Booking::STATUS_COMPLETED, 'vehicle' => 'DT 2203 CB', 'fuel' => 51.7],
            ['site' => 'Tambang Halmahera', 'days_ago' => 21, 'duration' => 10, 'status' => Booking::STATUS_COMPLETED, 'vehicle' => 'DB 5410 MS', 'fuel' => 62.4],
            ['site' => 'Tambang Obi', 'days_ago' => 17, 'duration' => 6, 'status' => Booking::STATUS_CONFIRMED, 'vehicle' => 'DN 7332 TR', 'fuel' => 29.8],
            ['site' => 'Tambang Weda', 'days_ago' => 12, 'duration' => 7, 'status' => Booking::STATUS_APPROVED_L2, 'vehicle' => 'B 1734 TMA', 'fuel' => null],
            ['site' => 'Kantor Cabang Kendari', 'days_ago' => 9, 'duration' => 5, 'status' => Booking::STATUS_APPROVED_L1, 'vehicle' => 'DD 8721 KM', 'fuel' => null],
            ['site' => 'Kantor Pusat Jakarta', 'days_ago' => 5, 'duration' => 4, 'status' => Booking::STATUS_SUBMITTED, 'vehicle' => 'DT 1101 HQ', 'fuel' => null],
            ['site' => 'Tambang Morowali', 'days_ago' => 3, 'duration' => 6, 'status' => Booking::STATUS_REJECTED, 'vehicle' => 'DT 2203 CB', 'fuel' => null],
            ['site' => 'Tambang Konawe', 'days_ago' => 1, 'duration' => 5, 'status' => Booking::STATUS_DRAFT, 'vehicle' => 'DN 7332 TR', 'fuel' => null],
        ];

        $activeDrivers = Driver::query()->where('status', 'active')->orderBy('id')->get();
        $driverIndex = 0;

        foreach ($bookingScenarios as $scenario) {
            $vehicle = Vehicle::query()->where('registration_no', $scenario['vehicle'])->firstOrFail();
            $driver = $activeDrivers[$driverIndex % $activeDrivers->count()];
            $driverIndex++;

            $startAt = now()->subDays($scenario['days_ago'])->setTime(8, 0);
            $endAt = $startAt->copy()->addHours($scenario['duration']);

            $booking = Booking::query()->updateOrCreate(
                ['user_id' => $admin->id, 'vehicle_id' => $vehicle->id, 'start_at' => $startAt],
                [
                    'driver_id' => $driver->id,
                    'approver_l1_id' => $approverL1->id,
                    'approver_l2_id' => $approverL2->id,
                    'origin_site_id' => $sites['Kantor Cabang Kendari']->id,
                    'destination_site_id' => $sites[$scenario['site']]->id,
                    'end_at' => $endAt,
                    'destination' => $scenario['site'],
                    'purpose' => 'Operasional dan monitoring area ' . $scenario['site'],
                    'status' => $scenario['status'],
                ]
            );

            $l1Status = match ($scenario['status']) {
                Booking::STATUS_DRAFT => 'pending',
                Booking::STATUS_SUBMITTED => 'pending',
                Booking::STATUS_REJECTED => 'rejected',
                default => 'approved',
            };

            $l2Status = match ($scenario['status']) {
                Booking::STATUS_APPROVED_L1, Booking::STATUS_SUBMITTED, Booking::STATUS_DRAFT => 'pending',
                Booking::STATUS_REJECTED => 'pending',
                default => 'approved',
            };

            Approval::query()->updateOrCreate(
                ['booking_id' => $booking->id, 'level' => 1],
                [
                    'approver_id' => $approverL1->id,
                    'status' => $l1Status,
                    'comment' => $l1Status === 'rejected' ? 'Jadwal bentrok dengan agenda prioritas.' : 'Disetujui untuk operasional.',
                    'acted_at' => $l1Status === 'pending' ? null : $startAt->copy()->subDay(),
                ]
            );

            Approval::query()->updateOrCreate(
                ['booking_id' => $booking->id, 'level' => 2],
                [
                    'approver_id' => $approverL2->id,
                    'status' => $l2Status,
                    'comment' => $l2Status === 'approved' ? 'Disetujui manajerial.' : null,
                    'acted_at' => $l2Status === 'approved' ? $startAt->copy()->subHours(12) : null,
                ]
            );

            if ($scenario['fuel'] !== null) {
                FuelConsumption::query()->updateOrCreate(
                    ['booking_id' => $booking->id],
                    [
                        'vehicle_id' => $vehicle->id,
                        'fuel_used' => $scenario['fuel'],
                        'recorded_at' => $endAt,
                    ]
                );
            }

            if (in_array($scenario['status'], [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED], true)) {
                $odometerStart = max(1, (int) $vehicle->mileage - random_int(80, 220));
                $odometerEnd = $odometerStart + random_int(35, 180);

                VehicleUsage::query()->updateOrCreate(
                    ['booking_id' => $booking->id],
                    [
                        'vehicle_id' => $vehicle->id,
                        'driver_id' => $driver->id,
                        'origin_site_id' => $sites['Kantor Cabang Kendari']->id,
                        'destination_site_id' => $sites[$scenario['site']]->id,
                        'started_at' => $startAt,
                        'ended_at' => $endAt,
                        'odometer_start' => $odometerStart,
                        'odometer_end' => $odometerEnd,
                        'notes' => 'Realisasi perjalanan operasional ke ' . $scenario['site'],
                    ]
                );
            }
        }

        $serviceRecords = [
            ['registration_no' => 'B 9451 UXP', 'service_date' => now()->subDays(20)->toDateString(), 'service_type' => 'Perawatan Berkala 20.000 KM', 'workshop_name' => 'PT Nusa Servis Kendari', 'cost' => 4850000, 'status' => 'done', 'notes' => 'Ganti oli, filter solar, spooring.'],
            ['registration_no' => 'DT 2203 CB', 'service_date' => now()->subDays(8)->toDateString(), 'service_type' => 'Perbaikan Sistem Pendingin', 'workshop_name' => 'Bengkel Mitra Morowali', 'cost' => 2750000, 'status' => 'done', 'notes' => 'Radiator dibersihkan dan coolant diganti.'],
            ['registration_no' => 'DB 5410 MS', 'service_date' => now()->addDays(6)->toDateString(), 'service_type' => 'Servis Rem dan Kaki-kaki', 'workshop_name' => 'CV Armada Teknik', 'cost' => 3200000, 'status' => 'scheduled', 'notes' => 'Jadwal service sebelum trip lintas site.'],
            ['registration_no' => 'DT 1101 HQ', 'service_date' => now()->addDays(12)->toDateString(), 'service_type' => 'Perawatan Berkala 10.000 KM', 'workshop_name' => 'Auto2000 Kendari', 'cost' => 1650000, 'status' => 'scheduled', 'notes' => 'Cek umum unit operasional tambang.'],
        ];

        foreach ($serviceRecords as $record) {
            $vehicle = Vehicle::query()->where('registration_no', $record['registration_no'])->firstOrFail();

            VehicleService::query()->updateOrCreate(
                ['vehicle_id' => $vehicle->id, 'service_date' => $record['service_date'], 'service_type' => $record['service_type']],
                [
                    'workshop_name' => $record['workshop_name'],
                    'cost' => $record['cost'],
                    'status' => $record['status'],
                    'notes' => $record['notes'],
                ]
            );
        }
    }
}
