<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        $now      = now();

        DB::table('users')->insert([
            [
                'id'         => 'a0000000-0000-0000-0000-000000000001',
                'name'       => 'João Silva',
                'cpf'        => '11122233309',
                'email'      => 'admin@biblioteca.com',
                'role'       => 'admin',
                'password'   => $password,
                'blocked'    => false,
                'created_at' => '2026-01-01 08:00:00',
                'updated_at' => '2026-01-01 08:00:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 'a0000000-0000-0000-0000-000000000002',
                'name'       => 'Maria Oliveira',
                'cpf'        => '22233344415',
                'email'      => 'maria.oliveira@biblioteca.com',
                'role'       => 'librarian',
                'password'   => $password,
                'blocked'    => false,
                'created_at' => '2026-01-02 08:00:00',
                'updated_at' => '2026-01-02 08:00:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 'a0000000-0000-0000-0000-000000000003',
                'name'       => 'Carlos Santos',
                'cpf'        => '33344455520',
                'email'      => 'carlos.santos@biblioteca.com',
                'role'       => 'librarian',
                'password'   => $password,
                'blocked'    => false,
                'created_at' => '2026-01-03 08:00:00',
                'updated_at' => '2026-01-03 08:00:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 'a0000000-0000-0000-0000-000000000004',
                'name'       => 'Ana Costa',
                'cpf'        => '44455566628',
                'email'      => 'ana.costa@email.com',
                'role'       => 'customer',
                'password'   => $password,
                'blocked'    => false,
                'created_at' => '2026-02-10 10:00:00',
                'updated_at' => '2026-02-10 10:00:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 'a0000000-0000-0000-0000-000000000005',
                'name'       => 'Pedro Alves',
                'cpf'        => '55566677731',
                'email'      => 'pedro.alves@email.com',
                'role'       => 'customer',
                'password'   => $password,
                'blocked'    => false,
                'created_at' => '2026-02-15 11:00:00',
                'updated_at' => '2026-02-15 11:00:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 'a0000000-0000-0000-0000-000000000006',
                'name'       => 'Fernanda Lima',
                'cpf'        => '66677788836',
                'email'      => 'fernanda.lima@email.com',
                'role'       => 'customer',
                'password'   => $password,
                'blocked'    => false,
                'created_at' => '2026-03-01 09:00:00',
                'updated_at' => '2026-03-01 09:00:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 'a0000000-0000-0000-0000-000000000007',
                'name'       => 'Rafael Souza',
                'cpf'        => '77788899940',
                'email'      => 'rafael.souza@email.com',
                'role'       => 'customer',
                'password'   => $password,
                'blocked'    => true,
                'created_at' => '2026-03-05 14:00:00',
                'updated_at' => '2026-05-10 09:00:00',
                'deleted_at' => null,
            ],
            [
                'id'         => 'a0000000-0000-0000-0000-000000000008',
                'name'       => 'Beatriz Mendes',
                'cpf'        => '88899900053',
                'email'      => 'beatriz.mendes@email.com',
                'role'       => 'customer',
                'password'   => $password,
                'blocked'    => false,
                'created_at' => '2026-03-20 16:00:00',
                'updated_at' => '2026-03-20 16:00:00',
                'deleted_at' => null,
            ],
        ]);
    }
}
