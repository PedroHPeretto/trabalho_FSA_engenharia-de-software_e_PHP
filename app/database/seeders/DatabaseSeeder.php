<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        DB::table('logs')->truncate();
        DB::table('fines')->truncate();
        DB::table('reservations')->truncate();
        DB::table('loans')->truncate();
        DB::table('books')->truncate();
        DB::table('password_reset_tokens')->truncate();
        DB::table('users')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->call([
            UserSeeder::class,
            BookSeeder::class,
            LoanSeeder::class,
            FineSeeder::class,
            ReservationSeeder::class,
            LogSeeder::class,
            BookMediaSeeder::class,
        ]);
    }
}
