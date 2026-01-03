<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

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

        // Render custom error pages for specific exceptions
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access Denied'], 403);
            }
            return response()->view('errors.403', ['exception' => $e], 403);
        });

        $this->renderable(function (Throwable $e, Request $request) {
            // Only render 500 page for server errors (not validation or auth errors)
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                return null; // Let Laravel handle HTTP exceptions
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Server Error'], 500);
            }

            // For demo: always show custom 500 page for server errors
            if (!($e instanceof \Illuminate\Validation\ValidationException) && 
                !($e instanceof \Illuminate\Auth\AuthenticationException)) {
                return response()->view('errors.500', ['exception' => $e], 500);
            }
        });
    }
}
