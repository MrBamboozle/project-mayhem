<?php

namespace App\Http\Controllers;

use App\Enums\AllowedParams;
use App\Enums\FilterableSortableModels;
use App\Enums\Operators;
use App\Models\User;
use App\Services\ModelService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(readonly ModelService $modelService)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): array
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

        return [
            $query->paginate(3),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return ['test' => 'mali pimpek'];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return ['test' => 'mali pimpek'];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return ['test' => 'mali pimpek'];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return ['test' => 'mali pimpek'];
    }
}
