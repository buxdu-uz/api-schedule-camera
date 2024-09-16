<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\PersonalService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function __construct(protected PersonalService $personal)
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Begin Creating Admin Roles" . PHP_EOL;
        echo "Creating Roles = OK" . PHP_EOL;
        echo "Starting Migrate Hemis Employeds" . PHP_EOL;
        $this->personal->hemisMigration('all');
        $this->createFirstRoles();
    }

    public function createFirstRoles(): void
    {
        $this->createAdminUser();
        Role::updateOrCreate(['name' => 'camera']);
        Role::updateOrCreate(['name' => 'manager']);
        Role::updateOrCreate(['name' => 'personal']);
        Role::updateOrCreate(['name' => 'finance']);
        Role::updateOrCreate(['name' => 'teacher']);
        Role::updateOrCreate(['name' => 'dean']);
        Role::updateOrCreate(['name' => 'controller']);
        Role::updateOrCreate(['name' => 'student']);
        Role::updateOrCreate(['name' => 'education']);
    }

    public function createAdminUser(): void
    {
        $role = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'all']);
        $role->givePermissionTo($permission);
        $admin = User::create([
            'name' => 'Administarator',
            'login' => 'admin',
            'employee_id' => '10000001',
            'password' => 'admin',
        ]);

        $admin->assignRole($role);
    }
}
