<?php

namespace App\Exceptions;

use App\Exceptions\Exceptions\ApiModelAccessDenied;
use App\Exceptions\Exceptions\ApiModelNotFoundException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;
use function Termwind\render;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Exception|Throwable $e): Response|JsonResponse|RedirectResponse|SymfonyResponse
    {
        $exception = match ($e::class) {
            ModelNotFoundException::class => (
                new ApiModelNotFoundException(
                    $e->getIds(),
                    $e->getModel()
                ))->render($request),
            AuthorizationException::class => (new ApiModelAccessDenied())->render($request),
            default => parent::render($request, $e),
        };

        return $exception;
    }
}
