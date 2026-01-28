<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'name' => 'TI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Financeiro',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Contabilidade',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Compras',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
