<?php

namespace App\Http\Controllers;

use App\Enums\QueryField;
use App\Enums\JsonFieldNames;
use App\Exceptions\Exceptions\FailToAddAvatarException;
use App\Exceptions\Exceptions\FailToDeleteCurrentAvatar;
use App\Exceptions\Exceptions\NonMatchingPasswordsException;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Avatar;
use App\Models\User;
use App\Services\ModelService;use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

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
        return $this->userService->createUser($request->validated())->toArray();
    }

    public function show(User $user): array
    {
        return $user->toArray();
    }

    public function update(UserUpdateRequest $request, User $user): array
    {
        $user->update($request->validated());

        return $user->toArray();
    }

    public function destroy(User $user): array
    {
        $user->delete();

        return [JsonFieldNames::MESSAGE->value => "User $user->name with id $user->id deleted"];
    }

    /**
     * @throws FailToAddAvatarException|FailToDeleteCurrentAvatar
     */
    public function addAvatar(Request $request, User $user, Avatar $avatar = null): array
    {
        return $this->userService->updateAvatar($user, $avatar, $request->file('avatar'))->toArray();
    }

    /**
     * @throws NonMatchingPasswordsException
     */
    public function changePassword(ChangePasswordRequest $request, User $user): array
    {
        $passwords = $request->validated();

        if (!Hash::check($passwords[JsonFieldNames::PASSWORD->value . 'Old'], $user->password)) {
            throw new NonMatchingPasswordsException('Unable to change password, wrong old password provided');
        }

        $user->password = Hash::make($passwords[JsonFieldNames::PASSWORD->value]);
        $user->save();

        return [
            JsonFieldNames::MESSAGE->value => 'Password changed successfully'
        ];
    }
}
