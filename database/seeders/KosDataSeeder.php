<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\Payment;
use App\Models\OperationalCost;
use App\Models\FinancialReport;
use App\Models\User;
use Carbon\Carbon;

class KosDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@kos.com'],
            [
                'name' => 'Admin Kos',
                'password' => bcrypt('password'),
            ]
        );

        // Create Rooms
        $rooms = [
            [
                'room_number' => 'A01',
                'room_type' => 'standard',
                'monthly_price' => 1000000,
                'capacity' => 1,
                'description' => 'Kamar standard dengan kasur single, lemari, dan meja belajar',
                'status' => 'occupied',
            ],
            [
                'room_number' => 'A02',
                'room_type' => 'standard',
                'monthly_price' => 1000000,
                'capacity' => 1,
                'description' => 'Kamar standard dengan kasur single, lemari, dan meja belajar',
                'status' => 'occupied',
            ],
            [
                'room_number' => 'B01',
                'room_type' => 'deluxe',
                'monthly_price' => 1500000,
                'capacity' => 2,
                'description' => 'Kamar deluxe dengan AC, TV, dan kamar mandi dalam',
                'status' => 'occupied',
            ],
            [
                'room_number' => 'B02',
                'room_type' => 'deluxe',
                'monthly_price' => 1500000,
                'capacity' => 2,
                'description' => 'Kamar deluxe dengan AC, TV, dan kamar mandi dalam',
                'status' => 'available',
            ],
            [
                'room_number' => 'C01',
                'room_type' => 'premium',
                'monthly_price' => 2000000,
                'capacity' => 2,
                'description' => 'Kamar premium dengan AC, TV, kulkas, kamar mandi dalam, dan balkon',
                'status' => 'available',
            ],
            [
                'room_number' => 'C02',
                'room_type' => 'premium',
                'monthly_price' => 2000000,
                'capacity' => 2,
                'description' => 'Kamar premium dengan AC, TV, kulkas, kamar mandi dalam, dan balkon',
                'status' => 'maintenance',
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::create($roomData);
        }

        // Create Tenants
        $tenants = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@email.com',
                'phone' => '081234567890',
                'identity_number' => '3201234567890001',
                'identity_type' => 'ktp',
                'identity_expiry_date' => null,
                'address' => 'Jl. Merdeka No. 123',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12345',
                'birth_date' => '1995-05-15',
                'status' => 'active',
                'notes' => 'Penyewa sudah 1 tahun, pembayaran selalu tepat waktu',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@email.com',
                'phone' => '081234567891',
                'identity_number' => '3201234567890002',
                'identity_type' => 'ktp',
                'identity_expiry_date' => null,
                'address' => 'Jl. Sudirman No. 456',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40123',
                'birth_date' => '1998-08-20',
                'status' => 'active',
                'notes' => 'Mahasiswa, sangat rapi',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@email.com',
                'phone' => '081234567892',
                'identity_number' => '3201234567890003',
                'identity_type' => 'ktp',
                'identity_expiry_date' => null,
                'address' => 'Jl. Gatot Subroto No. 789',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60123',
                'birth_date' => '1997-03-10',
                'status' => 'active',
                'notes' => 'Karyawan swasta, sangat bertanggung jawab',
            ],
        ];

        $createdTenants = [];
        foreach ($tenants as $tenantData) {
            $createdTenants[] = Tenant::create($tenantData);
        }

        // Create Payments for current month
        $currentMonth = Carbon::now()->startOfMonth();
        $payments = [
            [
                'tenant_id' => $createdTenants[0]->id,
                'room_id' => 1, // Room A01
                'amount' => 1000000,
                'payment_method' => 'transfer',
                'status' => 'confirmed',
                'payment_date' => $currentMonth->copy()->addDays(5),
                'due_date' => $currentMonth->copy()->addDays(10),
                'month_year' => $currentMonth,
                'reference_code' => Payment::generateReferenceCode(),
                'notes' => 'Pembayaran bulan ' . $currentMonth->format('F Y'),
            ],
            [
                'tenant_id' => $createdTenants[1]->id,
                'room_id' => 2, // Room A02
                'amount' => 1000000,
                'payment_method' => 'cash',
                'status' => 'confirmed',
                'payment_date' => $currentMonth->copy()->addDays(3),
                'due_date' => $currentMonth->copy()->addDays(10),
                'month_year' => $currentMonth,
                'reference_code' => Payment::generateReferenceCode(),
                'notes' => 'Pembayaran bulan ' . $currentMonth->format('F Y'),
            ],
            [
                'tenant_id' => $createdTenants[2]->id,
                'room_id' => 3, // Room B01
                'amount' => 1500000,
                'payment_method' => 'transfer',
                'status' => 'pending',
                'payment_date' => $currentMonth->copy()->addDays(8),
                'due_date' => $currentMonth->copy()->addDays(10),
                'month_year' => $currentMonth,
                'reference_code' => Payment::generateReferenceCode(),
                'notes' => 'Menunggu konfirmasi transfer',
            ],
        ];

        foreach ($payments as $paymentData) {
            Payment::create($paymentData);
        }

        // Create Operational Costs
        $costs = [
            [
                'created_by' => $admin->id,
                'category' => 'electricity',
                'description' => 'Tagihan listrik bulan ' . $currentMonth->format('F Y'),
                'amount' => 500000,
                'cost_date' => $currentMonth->copy()->addDays(15),
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => $currentMonth->copy()->addDays(16),
            ],
            [
                'created_by' => $admin->id,
                'category' => 'water',
                'description' => 'Tagihan air PDAM bulan ' . $currentMonth->format('F Y'),
                'amount' => 200000,
                'cost_date' => $currentMonth->copy()->addDays(15),
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => $currentMonth->copy()->addDays(16),
            ],
            [
                'created_by' => $admin->id,
                'category' => 'cleaning',
                'description' => 'Gaji cleaning service',
                'amount' => 1000000,
                'cost_date' => $currentMonth->copy()->addDays(25),
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => $currentMonth->copy()->addDays(26),
            ],
            [
                'created_by' => $admin->id,
                'category' => 'maintenance',
                'description' => 'Perbaikan AC kamar C02',
                'amount' => 350000,
                'cost_date' => $currentMonth->copy()->addDays(20),
                'status' => 'recorded',
            ],
        ];

        foreach ($costs as $costData) {
            OperationalCost::create($costData);
        }

        // Create Financial Report for last month
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd = $lastMonth->copy()->endOfMonth();

        FinancialReport::create([
            'report_type' => 'monthly',
            'period_start' => $lastMonthStart,
            'period_end' => $lastMonthEnd,
            'total_income' => 3500000, // 3 kamar terisi
            'total_operational_cost' => 2050000,
            'net_profit' => 1450000,
            'outstanding_payment' => 0,
            'total_tenants' => 3,
            'occupied_rooms' => 3,
            'available_rooms' => 3,
            'summary' => 'Laporan keuangan bulan ' . $lastMonth->format('F Y') . '. Total pemasukan Rp 3.500.000, biaya operasional Rp 2.050.000, laba bersih Rp 1.450.000. Tingkat okupansi 50%.',
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $this->command->info('✅ Demo data berhasil dibuat!');
        $this->command->info('📧 Email Admin: admin@kos.com');
        $this->command->info('🔑 Password: password');
    }
}
