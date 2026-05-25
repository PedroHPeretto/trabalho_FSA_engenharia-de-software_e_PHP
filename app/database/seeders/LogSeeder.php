<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('logs')->insert([
            [
                'id'          => 'e0000000-0000-0000-0000-000000000001',
                'made_by'     => 'a0000000-0000-0000-0000-000000000001',
                'action'      => 'USER_CREATED',
                'description' => 'Usuário Rafael Souza (rafael.souza@email.com) cadastrado no sistema.',
                'date'        => '2026-03-05 14:00:00',
                'created_at'  => '2026-03-05 14:00:00',
                'updated_at'  => '2026-03-05 14:00:00',
            ],
            [
                'id'          => 'e0000000-0000-0000-0000-000000000002',
                'made_by'     => 'a0000000-0000-0000-0000-000000000002',
                'action'      => 'LOAN_CREATED',
                'description' => 'Empréstimo registrado: Crime e Castigo para Rafael Souza. Devolução prevista: 2026-05-04.',
                'date'        => '2026-04-20 09:00:00',
                'created_at'  => '2026-04-20 09:00:00',
                'updated_at'  => '2026-04-20 09:00:00',
            ],
            [
                'id'          => 'e0000000-0000-0000-0000-000000000003',
                'made_by'     => 'a0000000-0000-0000-0000-000000000002',
                'action'      => 'FINE_GENERATED',
                'description' => 'Multa gerada para Rafael Souza. Empréstimo vencido em 2026-05-04. Valor: R$ 100,00.',
                'date'        => '2026-05-05 08:00:00',
                'created_at'  => '2026-05-05 08:00:00',
                'updated_at'  => '2026-05-05 08:00:00',
            ],
            [
                'id'          => 'e0000000-0000-0000-0000-000000000004',
                'made_by'     => 'a0000000-0000-0000-0000-000000000001',
                'action'      => 'USER_BLOCKED',
                'description' => 'Usuário Rafael Souza bloqueado por possuir multa em aberto.',
                'date'        => '2026-05-10 09:00:00',
                'created_at'  => '2026-05-10 09:00:00',
                'updated_at'  => '2026-05-10 09:00:00',
            ],
            [
                'id'          => 'e0000000-0000-0000-0000-000000000005',
                'made_by'     => 'a0000000-0000-0000-0000-000000000003',
                'action'      => 'FINE_PAID',
                'description' => 'Multa de R$ 25,00 quitada por Fernanda Lima referente ao empréstimo de O Senhor dos Anéis.',
                'date'        => '2026-03-25 10:00:00',
                'created_at'  => '2026-03-25 10:00:00',
                'updated_at'  => '2026-03-25 10:00:00',
            ],
        ]);
    }
}
