<?php

namespace App\Http\Controllers;

use App\Enums\AllowedParams;
use App\Enums\FilterableSortableModels;
use App\Enums\JsonFieldNames;
use App\Enums\Operators;
use App\Exceptions\Exceptions\ApiModelNotFoundException;
use App\Exceptions\Exceptions\FailToAddAvatarException;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Avatar;
use App\Models\User;
use App\Services\ModelService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function __construct(readonly ModelService $modelService)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): LengthAwarePaginator
    {
        $model = FilterableSortableModels::USER;
        /** @var Builder $query */
        $query = $model->value::query();

        $query = $this->modelService
            ->applyFilters(
                $query,
                Operators::LIKE,
                $request->query(AllowedParams::FILTER->value),
                $model
            );
        $query = $this->modelService->applySorts(
            $query,
            $request->query(AllowedParams::SORT->value),
            $model
        );

        return $query->paginate(3);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $user = User::factory()->createOne($request->validated());

        return [JsonFieldNames::USER->value => $user];
    }

    /**
     * Display the specified resource.
     * @throws ApiModelNotFoundException
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ApiModelNotFoundException($id, User::class);
        }

        return [JsonFieldNames::USER->value => $user];
    }

    /**
     * Update the specified resource in storage.
     * @throws ApiModelNotFoundException
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $data = $request->validated();

        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ApiModelNotFoundException($id, User::class);
        }

        $user->update($data);

        return [JsonFieldNames::USER->value => $user];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ApiModelNotFoundException($id, User::class);
        }

        $user->delete();

        return [JsonFieldNames::MESSAGE->value => "User $user->name with id $id deleted"];
    }

    /**
     * @throws ApiModelNotFoundException
     * @throws FailToAddAvatarException
     */
    public function addAvatar(Request $request, string $id): array
    {
        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new ApiModelNotFoundException($id, User::class);
        }

        $file = $request->file('avatar');

        if (empty($file)) {
            throw new FailToAddAvatarException('Missing upload file');
        }

        // We have defined $with on User model which always returns avatar without all values,
        // this way we get the whole Avatar object
        // if we don't do this Avatar object won't have "default" field and this will fail
        $currentAvatar = $user->avatar()->first();
        // getRawOriginal() bypasses the defined "path" accessor on Avatar model
        $currentAvatarPath = $currentAvatar->getRawOriginal('path');
        $path = $file->store('public/avatars');

        DB::beginTransaction();

        try {
            $newAvatar = Avatar::factory()->create([
                'path' => $path,
                'default' => false,
            ]);

            $user->update(['avatar_id' => $newAvatar->id]);

            if (!$currentAvatar->default) {
                $currentAvatar->delete();
                Storage::delete($currentAvatarPath);
            }
        } catch (\Throwable $error) {
            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            DB::rollBack();

            throw new FailToAddAvatarException($error->getMessage());
        }

        DB::commit();

        return [JsonFieldNames::MESSAGE->value => $newAvatar->path];
    }
}
