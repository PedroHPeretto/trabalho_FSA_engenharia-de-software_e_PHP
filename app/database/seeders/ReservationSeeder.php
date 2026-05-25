<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reservations')->insert([
            [
                'id'              => 'v0000000-0000-0000-0000-000000000001',
                'book_id'         => 'b0000000-0000-0000-0000-000000000003',
                'user_id'         => 'a0000000-0000-0000-0000-000000000004',
                'reserved_at'     => '2026-05-17 14:00:00',
                'expiration_date' => '2026-05-31 23:59:59',
                'status'          => 'pending',
                'created_at'      => '2026-05-17 14:00:00',
                'updated_at'      => '2026-05-17 14:00:00',
            ],
            [
                'id'              => 'v0000000-0000-0000-0000-000000000002',
                'book_id'         => 'b0000000-0000-0000-0000-000000000001',
                'user_id'         => 'a0000000-0000-0000-0000-000000000005',
                'reserved_at'     => '2026-05-08 10:00:00',
                'expiration_date' => '2026-05-15 23:59:59',
                'status'          => 'fulfilled',
                'created_at'      => '2026-05-08 10:00:00',
                'updated_at'      => '2026-05-10 10:00:00',
            ],
            [
                'id'              => 'v0000000-0000-0000-0000-000000000003',
                'book_id'         => 'b0000000-0000-0000-0000-000000000006',
                'user_id'         => 'a0000000-0000-0000-0000-000000000006',
                'reserved_at'     => '2026-04-10 09:00:00',
                'expiration_date' => '2026-04-17 23:59:59',
                'status'          => 'cancelled',
                'created_at'      => '2026-04-10 09:00:00',
                'updated_at'      => '2026-04-12 11:00:00',
            ],
        ]);
    }
}
