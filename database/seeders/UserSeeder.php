<?php

namespace Database\Seeders;

use App\Models\SupportGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $department = \App\Models\Department::first();
        /*
        |------------------------------------------------------------------
        | ADMINISTRADOR
        |------------------------------------------------------------------
        */
        $admin = User::firstOrCreate(
            ['email' => 'admin@grupoaste.com.br'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'agent_type' => 'operator',
                'department_id' => $department?->id,
                'is_active' => true,
            ]
        );

        /*
        |------------------------------------------------------------------
        | OPERADOR (SERVICE DESK)
        |------------------------------------------------------------------
        */
        $operator = User::firstOrCreate(
            ['email' => 'operador@grupoaste.com.br'],
            [
                'name' => 'Operador de Suporte',
                'password' => Hash::make('12345678'),
                'role' => 'agent',
                'agent_type' => 'operator',
                'department_id' => $department?->id,
                'is_active' => true,
            ]
        );

        /*
        |------------------------------------------------------------------
        | ESPECIALISTAS
        |------------------------------------------------------------------
        */
        $erpSpecialist = User::firstOrCreate(
            ['email' => 'erp@grupoaste.com.br'],
            [
                'name' => 'Especialista ERP SIGE',
                'password' => Hash::make('12345678'),
                'role' => 'agent',
                'agent_type' => 'specialist',
                'department_id' => $department?->id,
                'is_active' => true,
            ]
        );

        $pdvSpecialist = User::firstOrCreate(
            ['email' => 'pdv@grupoaste.com.br'],
            [
                'name' => 'Especialista PDV',
                'password' => Hash::make('12345678'),
                'role' => 'agent',
                'agent_type' => 'specialist',
                'department_id' => $department?->id,
                'is_active' => true,
            ]
        );

        /*
        |------------------------------------------------------------------
        | USUÁRIO SOLICITANTE
        |------------------------------------------------------------------
        */
        $user = User::firstOrCreate(
            ['email' => 'usuario@grupoaste.com.br'],
            [
                'name' => 'Usuário Solicitante',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'department_id' => $department?->id,
                'is_active' => true,
            ]
        );

       

    }
}
