<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];
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
     *
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception): \Illuminate\Http\JsonResponse
    {
        return $this->handleException($request, $exception);
    }
    /**
     * @param $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleException($request, Throwable $exception): \Illuminate\Http\JsonResponse
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json('The specified method for the request is invalid', 405);
        }

        if ($exception instanceof RouteNotFoundException || $exception instanceof NotFoundHttpException) {
            return response()->json('The specified URL cannot be found', 404);
        }

        if ($exception instanceof HttpException) {
            return response()->json($exception->getMessage(), $exception->getStatusCode());
        }

        if ($exception instanceof ValidationException) {

            $items= $exception->validator->errors()->getMessages();
            return response()->json($items, 422, $items);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json($exception->getMessage(), 401);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json($exception->getMessage(), 403);
        }

        return response()->json($exception->getMessage() . ' in ' . $exception->getFile() . ":" . $exception->getLine(), 500);
    }
}
