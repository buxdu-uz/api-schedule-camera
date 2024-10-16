<?php

namespace App\Http\Controllers\SubjectGroups;

use App\Domain\Classifiers\Models\ClassifierOption;
use App\Domain\EducationYears\Resources\EducationYearResource;
use App\Domain\SubjectGroups\Actions\StoreSubjectGroupAction;
use App\Domain\SubjectGroups\DTO\StoreSubjectGroupDTO;
use App\Domain\SubjectGroups\Repositories\SubjectGroupRepository;
use App\Domain\SubjectGroups\Requests\StoreSubjectGroupRequest;
use App\Domain\SubjectGroups\Requests\SubjectGroupFilterRequest;
use App\Domain\SubjectGroups\Resources\SubjectGroupResource;
use App\Domain\SubjectGroups\Resources\TeacherSubjectGroupResource;
use App\Filters\SubjectGroupFilter;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubjectGroupController extends Controller
{
    /**
     * @var mixed|SubjectGroupRepository
     */
    public mixed $subject_groups;

    /**
     * @param SubjectGroupRepository $subjectGroupRepository
     */
    public function __construct(SubjectGroupRepository $subjectGroupRepository)
    {
        $this->subject_groups = $subjectGroupRepository;
    }

    /**
     * @return JsonResponse
     */
    public function educationYears()
    {
        return $this->successResponse('',EducationYearResource::collection(ClassifierOption::query()
            ->where('classifier_id',56)
            ->where('code', '>=', date('Y'))
            ->get()
            ->sortBy('name'))
        );
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return SubjectGroupResource::collection($this->subject_groups->paginate());
    }

    public function getOwnSubjectGroup(SubjectGroupFilterRequest $request): JsonResponse
    {
        $filter = app()->make(SubjectGroupFilter::class, ['queryParams' => array_filter($request->validated())]);
        return response()->json( $this->subject_groups->getOwnSubjectGroup($filter));
    }

    /**
     * @param StoreSubjectGroupRequest $request
     * @param StoreSubjectGroupAction $action
     * @return JsonResponse|null
     */
    public function store(StoreSubjectGroupRequest $request, StoreSubjectGroupAction $action): ?JsonResponse
    {
        try {
            $dto = StoreSubjectGroupDTO::fromArray($request->validated());
            $response = $action->execute($dto);

            return $this->successResponse('Subject group created', new SubjectGroupResource($response));
        }catch (Exception $exception){
            return $this->errorResponse($exception->getMessage());
        }
    }
}
