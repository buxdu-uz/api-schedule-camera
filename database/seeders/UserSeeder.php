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

    public mixed $DEAN=25;          //dekan
    public mixed $DEAN_MUOVINI=26;  //zam dekan
    public mixed $MANAGER=16;       //kafedra mudiri
    public mixed $TEACHER=12;       //o'qituvchi
    public mixed $DEPARTMENT=17;       //bo'lim boshlig'i
    public mixed $VICE_RECTOR=22;       //O‘quv ishlari bo‘yicha birinchi prorektor
    public mixed $CHIEF_SPECIALIST=35;       //Bosh mutaxasis


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
        echo "Starting Migrate Hemis Employees" . PHP_EOL;
        $this->personal->hemisMigration('all');

        echo 'Start role dean';
        $this->personal->roleUser($this->DEAN);
        echo 'Start role dean muovini';
        $this->personal->roleUser($this->DEAN_MUOVINI);
        echo 'Start role dean manager';
        $this->personal->roleUser($this->MANAGER);
        echo 'Start role dean teacher';
        $this->personal->roleUser($this->TEACHER);
        echo 'Start role dean department';
        $this->personal->roleUser($this->DEPARTMENT);
        echo 'Start role dean vice_rector';
        $this->personal->roleUser($this->VICE_RECTOR);
        echo 'Start role dean chief specialist';
        $this->personal->roleUser($this->CHIEF_SPECIALIST);

//        echo "Start DEan muovini";
//        echo "Start bo\'lim boshlig'i";
//        $this->personal->hemisMigration('all',$this->MANAGER,12);
//        echo "Start o'qituvchi";
//        $this->personal->hemisMigration('all',$this->TEACHER,12);
//        echo "Start department";
//        $this->personal->hemisMigration('all',$this->DEPARTMENT,10);
//        echo "Start prorektor uquv ishlari";
//        $this->personal->hemisMigration('all',$this->VICE_RECTOR,10);
//        echo "Start bosh mutaxasis";
//        $this->personal->hemisMigration('all',$this->CHIEF_SPECIALIST,11);
    }

    public function createFirstRoles(): void
    {
//        $this->createAdminUser();
        $role = Role::updateOrCreate(['name' => 'admin']);
        $permission = Permission::create(['name' => 'all']);
        $role->givePermissionTo($permission);
        Role::updateOrCreate(['name' => 'dean']);
        Role::updateOrCreate(['name' => 'dean_deputy']);
        Role::updateOrCreate(['name' => 'manager']);
        Role::updateOrCreate(['name' => 'teacher']);
        Role::updateOrCreate(['name' => 'department']);
        Role::updateOrCreate(['name' => 'vice_rector']);
        Role::updateOrCreate(['name' => 'chief_specialist']);


        $admin = User::create([
            'name' => 'Administarator',
            'login' => 'admin',
            'employee_id' => '10000001',
            'password' => 'admin',
        ]);
        $admin->syncRoles(['admin','dean','dean_deputy','manager','teacher','department','vice_rector','chief_specialist']);
    }

//    public function createAdminUser(): void
//    {
//        $role = Role::updateOrCreate(['name' => 'admin']);
//        $permission = Permission::create(['name' => 'all']);
//        $role->givePermissionTo($permission);
//        $admin = User::create([
//            'name' => 'Administarator',
//            'login' => 'admin',
//            'employee_id' => '10000001',
//            'password' => 'admin',
//        ]);
//        $admin->syncRoles($role->name);
//    }
}
