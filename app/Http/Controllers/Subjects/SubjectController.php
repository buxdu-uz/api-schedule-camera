<?php

namespace App\Http\Controllers\Subjects;

use App\Domain\Subjects\Repositories\SubjectRepository;
use App\Domain\Subjects\Resources\SubjectResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * @var mixed|SubjectRepository
     */
    public mixed $subjects;

    /**
     * @param SubjectRepository $subjectRepository
     */
    public function __construct(SubjectRepository $subjectRepository)
    {
        $this->subjects = $subjectRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        return $this->successResponse('',SubjectResource::collection($this->subjects->getAll()));
    }

    /**
     * @return JsonResponse
     */
    public function paginate(): JsonResponse
    {
        return $this->successResponse('',SubjectResource::collection($this->subjects->paginate()));
    }
}
