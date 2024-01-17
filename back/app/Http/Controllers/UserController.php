<?php

namespace App\Http\Controllers;

use App\Enums\QueryField;
use App\Enums\JsonFieldNames;
use App\Exceptions\Exceptions\ApiModelNotFoundException;
use App\Exceptions\Exceptions\FailToAddAvatarException;
use App\Exceptions\Exceptions\FailToDeleteCurrentAvatar;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Avatar;
use App\Models\User;
use App\Services\ModelService;use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct(
        protected readonly ModelService $modelService,
        protected readonly UserService $userService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): LengthAwarePaginator
    {
        $query = User::query();

        $query = $this->modelService
            ->applyFilters(
                $query,
                $request->query(QueryField::FILTER->value),
            );
        $query = $this->modelService->applySorts(
            $query,
            $request->query(QueryField::SORT->value),
        );

        return $query->paginate(3);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): array
    {
        $user = $this->userService->createUser($request->validated());

        return $user->toArray();
    }

    /**
     * Display the specified resource.
     * @throws ApiModelNotFoundException
     */
    public function show(string $id): array
    {
        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ApiModelNotFoundException($id, User::class);
        }

        return $user->toArray();
    }

    /**
     * Update the specified resource in storage.
     * @throws ApiModelNotFoundException
     */
    public function update(UserUpdateRequest $request, string $id): array
    {
        $data = $request->validated();

        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ApiModelNotFoundException($id, User::class);
        }

        $user->update($data);

        return $user->toArray();
    }

    /**
     * Remove the specified resource from storage.
     * @throws ApiModelNotFoundException
     */
    public function destroy(string $id): array
    {
        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ApiModelNotFoundException($id, User::class);
        }

        $user->delete();

        return [JsonFieldNames::MESSAGE->value => "User $user->name with id $id deleted"];
    }

    /**
     * @throws ApiModelNotFoundException
     * @throws FailToAddAvatarException|FailToDeleteCurrentAvatar
     */
    public function addAvatar(Request $request, string $userId, string $avatarId = null): array
    {
        try {
            $user = User::findOrFail($userId);
            $avatar = null;

            if (!empty($avatarId)) {
                $avatar = Avatar::findOrFail($avatarId);
            }
        } catch (ModelNotFoundException $exception) {
            if (Str::contains(($exception->getMessage()), Avatar::class)) {
                throw new ApiModelNotFoundException($avatarId, Avatar::class);
            }

            throw new ApiModelNotFoundException($userId, User::class);
        }

        return $this->userService->updateAvatar($user, $avatar, $request->file('avatar'))->toArray();
    }
}
