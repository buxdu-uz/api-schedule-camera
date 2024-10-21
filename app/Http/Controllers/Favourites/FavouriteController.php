<?php

namespace App\Http\Controllers\Favourites;

use App\Domain\Favourites\Actions\StoreFavouriteAction;
use App\Domain\Favourites\Actions\UpdateFavouriteAction;
use App\Domain\Favourites\DTO\StoreFavouriteDTO;
use App\Domain\Favourites\DTO\UpdateFavouriteDTO;
use App\Domain\Favourites\Models\Favourite;
use App\Domain\Favourites\Repositories\FavouriteRepository;
use App\Domain\Favourites\Requests\StoreFavouriteRequest;
use App\Domain\Favourites\Requests\UpdateFavouriteRequest;
use App\Domain\Favourites\Resources\FavouriteResource;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function Laravel\Prompts\error;

class FavouriteController extends Controller
{
    /**
     * @var mixed|FavouriteRepository
     */
    public mixed $favourites;

    /**
     * @param FavouriteRepository $favouriteRepository
     */
    public function __construct(FavouriteRepository $favouriteRepository)
    {
        $this->favourites = $favouriteRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse('', $this->favourites->paginate());
    }

    /**
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        return $this->successResponse('', FavouriteResource::collection($this->favourites->getAll()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFavouriteRequest $request, StoreFavouriteAction $action): ?JsonResponse
    {
        try {
            $dto = StoreFavouriteDTO::fromArray($request->validated());
            $response = $action->execute($dto);

            return $this->successResponse('', new FavouriteResource($response));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param Favourite $favourite
     * @return JsonResponse
     */
    public function attachCameraToFavourite(Request $request, Favourite $favourite)
    {
        $request->validate([
            'cameras' => 'required|array'
        ]);

        $favourite->cameras()->attach($request->cameras);

        return $this->successResponse('Favourite section attach cameras.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFavouriteRequest $request, Favourite $favourite, UpdateFavouriteAction $action): ?JsonResponse
    {
        try {
            $dto = UpdateFavouriteDTO::fromArray(array_merge($request->validated(), ['favourite' => $favourite]));
            $response = $action->execute($dto);

            return $this->successResponse('', new FavouriteResource($response));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favourite $favourite): JsonResponse
    {
        $favourite->delete();

        return $this->successResponse('Favourite section deleted successfully.');
    }
}
