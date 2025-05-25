<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv = Reader::createFromPath(database_path('seeders/data/role_permission_map.csv'), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            $role = Role::where('name', $record['role_name'])->first();
            $permission = Permission::where('name', $record['permission_name'])->first();

            if ($role && $permission) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
