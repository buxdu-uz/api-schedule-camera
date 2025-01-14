<?php

namespace App\Http\Controllers\Syllabus;

use App\Domain\Syllabus\Actions\StoreSyllabusAction;
use App\Domain\Syllabus\Actions\UpdateSyllabusAction;
use App\Domain\Syllabus\DTO\StoreSyllabusDTO;
use App\Domain\Syllabus\DTO\UpdateSyllabusDTO;
use App\Domain\Syllabus\Models\Syllabus;
use App\Domain\Syllabus\Repositories\SyllabusRepository;
use App\Domain\Syllabus\Requests\StoreSyllabusRequest;
use App\Domain\Syllabus\Requests\UpdateSyllabusRequest;
use App\Domain\Syllabus\Resources\SyllabusResource;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;

class SyllabusController extends Controller
{
    /**
     * @var mixed|SyllabusRepository
     */
    public mixed $syllabus;

    /**
     * @param SyllabusRepository $syllabusRepository
     */
    public function __construct(SyllabusRepository $syllabusRepository)
    {
        $this->syllabus = $syllabusRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        return $this->successResponse('', SyllabusResource::collection($this->syllabus->getAll()));
    }

    /**
     * @param StoreSyllabusRequest $request
     * @param StoreSyllabusAction $action
     * @return JsonResponse|null
     */
    public function store(StoreSyllabusRequest $request, StoreSyllabusAction $action): ?JsonResponse
    {
        try {
            $dto = StoreSyllabusDTO::fromArray($request->validated());
            $response = $action->execute($dto);

            return $this->successResponse('', new SyllabusResource($response));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function update(UpdateSyllabusRequest $request, Syllabus $syllabus,UpdateSyllabusAction $action): ?JsonResponse
    {
        try {
            $dto = UpdateSyllabusDTO::fromArray(array_merge($request->validated(),['syllabus' => $syllabus]));
            $response = $action->execute($dto);

            return $this->successResponse('', new SyllabusResource($response));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
