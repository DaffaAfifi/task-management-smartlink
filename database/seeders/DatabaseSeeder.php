<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin =  Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        $this->call(RolePermissionSeeder::class);

        Division::create(['name' => 'IT']);
        Division::create(['name' => 'HR']);
        Division::create(['name' => 'Operasional']);

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('123456'),
        ])->assignRole($superAdmin);

        User::factory()->create([
            'name' => 'Admin 1',
            'email' => 'admin1@example.com',
            'password' => bcrypt('123456'),
        ])->assignRole($admin);

        User::factory()->create([
            'name' => 'Rob Stark',
            'email' => 'user1@example.com',
            'password' => bcrypt('123456'),
            'division_id' => 1
        ])->assignRole($user);

        User::factory()->create([
            'name' => 'Sansa Stark',
            'email' => 'user2@example.com',
            'password' => bcrypt('123456'),
            'division_id' => 1
        ])->assignRole($user);

        User::factory()->create([
            'name' => 'Arya Stark',
            'email' => 'user3@example.com',
            'password' => bcrypt('123456'),
            'division_id' => 2
        ])->assignRole($user);

        $this->call(ProjectSeeder::class);

        $this->call(TaskSeeder::class);
    }
}
