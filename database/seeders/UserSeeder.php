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
    /**
     * @var mixed|PersonalService
     */
    public mixed $personal;

    /**
     * @param PersonalService $personalService
     */
    public function __construct(PersonalService $personalService)
    {
        $this->personal = $personalService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createFirstRoles();
        echo "Begin Creating Admin Roles" . PHP_EOL;
        echo "Creating Roles = OK" . PHP_EOL;
        echo "Starting Migrate Hemis Employeds" . PHP_EOL;
        $this->personal->hemisMigration('all');
    }

    public function createFirstRoles(): void
    {
        $this->createAdminUser();
        $role = Role::updateOrCreate(['name' => 'admin']);
        Role::updateOrCreate(['name' => 'manager']);
        Role::updateOrCreate(['name' => 'personal']);
        Role::updateOrCreate(['name' => 'teacher']);
        Role::updateOrCreate(['name' => 'dean']);
        $permission = Permission::create(['name' => 'all']);
        $role->givePermissionTo($permission);
    }

    public function createAdminUser(): void
    {
        $admin = User::create([
            'name' => 'Administarator',
            'login' => 'admin',
            'employee_id' => '10000001',
            'password' => 'admin',
        ]);
        $admin->syncRoles('admin');
    }
}
