<?php

namespace App\Http\Controllers\SubjectGroups;

use App\Domain\SubjectGroups\Actions\StoreSubjectGroupAction;
use App\Domain\SubjectGroups\DTO\StoreSubjectGroupDTO;
use App\Domain\SubjectGroups\Repositories\SubjectGroupRepository;
use App\Domain\SubjectGroups\Requests\StoreSubjectGroupRequest;
use App\Domain\SubjectGroups\Resources\SubjectGroupResource;
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
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return SubjectGroupResource::collection($this->subject_groups->paginate());
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
