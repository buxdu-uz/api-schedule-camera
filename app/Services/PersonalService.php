<?php

namespace App\Services;


use App\Domain\Classifiers\Models\ClassifierOption;
use App\Domain\Departments\Models\Department;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PersonalService
{
    /**
     * Run the database seeds.
     */
    /**
     * @throws \Throwable
     */
    public function hemisMigration($type): void
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
        $request = new Request(
            'GET',
            config('hemis.host') . 'data/employee-list?type=' . $type . '&limit=' . config('hemis.limit'),
            $headers
        );
        $res = $client->sendAsync($request)->wait();
        $res = $res->getBody();
        $result = json_decode($res);
        if ($result->success === true) {
//            if ($result->data->pagination->totalCount > config('hemis.limit')) {
            for ($i = 1; $i <= $result->data->pagination->pageCount; $i++) {
                if ($i === 1) {
                    $this->store($result,$type);
                } else {
                    $request = new Request(
                        'GET',
                        config('hemis.host') . 'data/employee-list?type=' . $type . '&limit=' . config('hemis.limit').'&page=' . $i,
                        $headers
                    );
                    $res = $client->sendAsync($request)->wait();
                    $res = $res->getBody();
                    $result = json_decode($res);
                    $this->store($result,$type);
                }
                if($type === 'teacher'){
                    echo '    Teachers page: ' . $i . '/' . $result->data->pagination->pageCount . ' Stored' . PHP_EOL;
                }else{
                    echo '    Employees page: ' . $i . '/' . $result->data->pagination->pageCount . ' Stored' . PHP_EOL;
                }
            }
//            }
        } else {
            $this->store($result,$type);
        }
    }

    /**
     * @throws \Throwable
     */
    public function store($result,$type): void
    {
        foreach (collect($result->data->items)->sortBy('id') as $item) {
            DB::beginTransaction();
            try {
                    $user = User::updateOrCreate([
                        'id' => $item->id,
                        'employee_id' => $item->employee_id_number,
                    ], [
                        'name' => $item->full_name,
                        'login' => $item->employee_id_number,
                        'password' => bcrypt($item->employee_id_number),
                        'avatar' => $item->image,
                    ]);

                $userProfile = $user->profile()->firstOrNew([
                    'user_id' => $user->id,
                ]);

                $userProfile->fill([
                    'department_id' => Department::getIdByCode($item->department->code) ?? null,
                    'full_name' => $item->full_name,
                    'short_name' => $item->short_name,
                    'first_name' => $item->first_name,
                    'second_name' => $item->second_name,
                    'third_name' => $item->third_name,
                    'year_of_enter' => $item->year_of_enter,
                    'gender' => ClassifierOption::getId('gender', $item->gender->code),
                    'h_academic_degree' => ClassifierOption::getId('academicDegree', $item->academicDegree->code),
                    'h_academic_rank' => ClassifierOption::getId('academicRank', $item->academicRank->code),
                    'h_employment_form' => ClassifierOption::getId('employmentForm', $item->employmentForm->code),
                    'h_employment_staff' => ClassifierOption::getId('employmentStaff', $item->employmentStaff->code),
                    'h_staff_position' => ClassifierOption::getId('teacherPositionType', $item->staffPosition->code),
                    'h_employee_status' => ClassifierOption::getId('employeeType', $item->employeeStatus->code),
                    'h_employee_type' => ClassifierOption::getId('employeeType', $item->employeeType->code),
                    'birth_date' => date('Y-m-d', $item->birth_date),
                    'contract_number' => $item->contract_number,
                    'decree_number' => $item->decree_number,
                    'contract_date' => date('Y-m-d', $item->contract_date),
                    'decree_date' => date('Y-m-d', $item->decree_date),
                    'tutorGroups' => json_encode($item->tutorGroups),
                ]);

                $userProfile->save();
                    $this->assignRoleToEmployee($user, $type, $item);
                DB::commit();
            } catch (\Exception $exception) {
                echo json_encode($item);
                DB::rollBack();
                throw $exception;
            }
        }
    }

    protected function assignRoleToEmployee(User $user, string $type, $item): void
    {
        if($type === 'teacher'){
            $roleName = 'teacher';
        }else{
            if($item->staffPosition->name == 'Dekan'){
                $roleName = 'dean';
            }elseif($item->staffPosition->name == 'Dekan muovini'){
                $roleName = 'dean_deputy';
            }elseif($item->staffPosition->name == 'Bo‘lim boshlig‘i'){
                $roleName = 'manager';
            }elseif($item->staffPosition->name == 'Bosh mutaxassis'){
                $roleName = 'chief_specialist';
            }else{
                $roleName = $item->staffPosition->name;
            }
        }
        $role = Role::updateOrCreate([
            'name' => Str::slug($roleName),
        ], [
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);
    }

    private function getAvatar($image): string
    {
        return Storage::disk('public')->put('avatars', $image);
    }



//            if($item->staffPosition->name == 'Dekan'){
//            $roleName = 'dean';
//        }elseif($item->staffPosition->name == 'Dekan muovini'){
//            $roleName = 'dean_deputy';
//        }elseif($item->staffPosition->name == 'Bo‘lim boshlig‘i'){
//            $roleName = 'manager';
//        }elseif($item->staffPosition->name == 'Bosh mutaxassis'){
//            $roleName = 'chief_specialist';
//        }
//        if($type === 'teacher'){
//            $roleName = 'teacher';
//        }
}
