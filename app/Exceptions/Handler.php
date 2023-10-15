<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    }

    public function render($request, Throwable $exception) {

        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        } else if ($exception instanceof ValidationException) {
            $errors = $exception->errors();
            return response()->json([
                'status' => 'invalid',
                'message' => 'Request body is not valid',
                'violations' => collect($errors)->map(function($msg) {
                    return ['message' => $msg[0]];
                })
            ], 400);
        }

        return parent::render($request, $exception);
    }
}
