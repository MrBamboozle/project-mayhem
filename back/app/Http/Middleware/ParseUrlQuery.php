<?php

namespace App\Http\Middleware;

use App\Enums\QueryField;
use App\Enums\Route;
use App\Services\UrlQuery\UrlQueryService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParseUrlQuery
{
    public function __construct(
        private readonly UrlQueryService $urlQueryService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeEnum = Route::create(explode('/', $request->path())[1]);

        if ($routeEnum->isUndefined()) {
            return $next($request);
        }

        $filters = $this->urlQueryService->createUrlFilters(
            $request->query(QueryField::FILTER->value),
            $routeEnum
        );
        $sorts = $this->urlQueryService->createUrlSorts(
            $request->query(QueryField::SORT->value),
            $routeEnum
        );

        $request->query->add([
            'filter' => $filters,
            'sort' => $sorts,
        ]);

        return $next($request);
    }
}
