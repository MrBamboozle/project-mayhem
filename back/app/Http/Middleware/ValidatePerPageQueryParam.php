<?php

namespace App\Http\Middleware;

use App\Enums\QueryField;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePerPageQueryParam
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $perPage = $request->query(QueryField::PER_PAGE->value) ?? null;

        if (empty($perPage) || filter_var($perPage, FILTER_VALIDATE_INT) === false) {
            $request->query->add([
                QueryField::PER_PAGE->value => 10,
            ]);
        }

        return $next($request);
    }
}
