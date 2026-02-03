<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | DEPARTAMENTOS FIXOS
        |--------------------------------------------------------------------------
        */
        $tiDepartment = Department::where('name', 'TI')->first();
        $comprasDepartment = Department::where('name', 'Compras')->first();

        if (! $tiDepartment || ! $comprasDepartment) {
            throw new \Exception(
                'Departamentos obrigatórios (TI, Compras) não encontrados.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | USUÁRIOS FIXOS (ÂNCORA)
        |--------------------------------------------------------------------------
        */

        // ADMINISTRADOR
        User::firstOrCreate(
            ['email' => 'admin@grupoaste.com.br'],
            [
                'name' => 'Administrador Sistema',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'department_id' => $tiDepartment->id,
                'is_active' => true,
            ]
        );

        // USUÁRIO SOLICITANTE
        User::firstOrCreate(
            ['email' => 'usuario@grupoaste.com.br'],
            [
                'name' => 'Usuário Solicitante',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'department_id' => $comprasDepartment->id,
                'is_active' => true,
            ]
        );

        // OPERADOR HELP DESK
        User::firstOrCreate(
            ['email' => 'operador@grupoaste.com.br'],
            [
                'name' => 'Operador Help Desk',
                'password' => Hash::make('12345678'),
                'role' => 'agent',
                'department_id' => $tiDepartment->id,
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | USUÁRIOS ALEATÓRIOS PARA TESTES
        |--------------------------------------------------------------------------
        */

        // ADMINS
        User::factory()
            ->count(5)
            ->create([
                'role' => 'admin',
                'department_id' => $tiDepartment->id,
            ]);

        // AGENTS
        User::factory()
            ->count(20)
            ->create([
                'role' => 'agent',
                'department_id' => $tiDepartment->id,
            ]);

        // USERS (SOLICITANTES)
        User::factory()
            ->count(30)
            ->create([
                'role' => 'user',
                'department_id' => $comprasDepartment->id,
            ]);
    }
}
