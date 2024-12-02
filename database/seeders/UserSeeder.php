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
        $role = Role::updateOrCreate(['name' => 'admin']);
        $permission = Permission::updateOrCreate(['name' => 'all']);
        $role->givePermissionTo($permission);
        $admin = User::updateOrcreate(['login' => 'admin'],[
            'name' => 'Administarator',
            'login' => 'admin',
            'employee_id' => '10000001',
            'password' => bcrypt('buxdu2024'),
        ]);
        $admin->assignRole($role);


        echo "Starting Migrate Hemis Employees" . PHP_EOL;
        $this->personal->hemisMigration('employee');

        echo "Starting Migrate Hemis Teacher" . PHP_EOL;
        $this->personal->hemisMigration('teacher');

        $roles = ['dean', 'teacher', 'dean-deputy', 'manager', 'chief-specialist'];
        // Assign roles to the admin
        foreach ($roles as $roleName) {
            $admin->assignRole($roleName);
        }
    }
}
