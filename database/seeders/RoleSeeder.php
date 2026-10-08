<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Superadmin',
            'admin',
            'CEO',
            'Corporation Secretary',
            'Director',
            'Division Head',
            'DD Internal Audit',
            'DD ICT',
            'Board Member',
            'Officer',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
