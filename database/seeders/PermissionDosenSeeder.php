<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionDosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dosenRole = Role::where('name', 'dosen')->first();

        if ($dosenRole) {
            $permission = Permission::firstOrCreate(['name' => 'page_InputNilaiPage', 'guard_name' => 'web']);
            $dosenRole->givePermissionTo($permission);
        }
    }
}