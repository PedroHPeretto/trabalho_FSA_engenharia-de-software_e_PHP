<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FineSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('fines')->insert([
            [
                'id'         => 'f0000000-0000-0000-0000-000000000001',
                'loan_id'    => 'l0000000-0000-0000-0000-000000000003',
                'user_id'    => 'a0000000-0000-0000-0000-000000000006',
                'amount'     => 25.00,
                'paid'       => true,
                'created_at' => '2026-03-20 11:00:00',
                'updated_at' => '2026-03-25 10:00:00',
            ],
            [
                'id'         => 'f0000000-0000-0000-0000-000000000002',
                'loan_id'    => 'l0000000-0000-0000-0000-000000000004',
                'user_id'    => 'a0000000-0000-0000-0000-000000000007',
                'amount'     => 100.00,
                'paid'       => false,
                'created_at' => '2026-05-05 08:00:00',
                'updated_at' => '2026-05-05 08:00:00',
            ],
        ]);
    }
}
