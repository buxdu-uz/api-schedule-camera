<?php

namespace App\Http\Controllers\Syllabus;

use App\Domain\Syllabus\Actions\StoreSyllabusAction;
use App\Domain\Syllabus\DTO\StoreSyllabusDTO;
use App\Domain\Syllabus\Repositories\SyllabusRepository;
use App\Domain\Syllabus\Requests\StoreSyllabusRequest;
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
    public function latest(): JsonResponse
    {
        if ($this->syllabus->latest()) {
            return $this->successResponse('', new SyllabusResource($this->syllabus->latest()));
        }

        return $this->errorResponse('Mavjud emas');
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
}
