<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
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

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Not found',
                'errors' => [
                  'The URL you are trying to access does not exist'
                ]
            ], 404);
        }

        if ($exception instanceof UnauthorizedException) {
          return response()->json([
              'success' => false,
              'code' => 401,
              'message' => 'Unauthorized',
              'errors' => [
                'You are not authorized to access this resource',
              ]
          ], 401);
        }
        
        if($exception instanceof AuthenticationException) {
          return response()->json([
              'success' => false,
              'code' => 401,
              'message' => 'Unauthorized',
              'errors' => [
                'You are not authorized to access this resource',
              ]
          ], 401);
        }

        if($exception instanceof AuthorizationException) {
          return response()->json([
              'success' => false,
              'code' => 401,
              'message' => 'Unauthorized',
              'errors' => [
                'You are not authorized to access this resource',
              ]
          ], 401);
        }

        if ($exception instanceof ModelNotFoundException) {
          return response()->json([
            'success' => false,
            'code' => 404,
            'message' => 'Not Found',
            'errors' => [
              'Model Not Found',
            ]
        ], 404);
      }

        $statusCode = $this->isHttpException($exception) ? $exception->getStatusCode() : 500;
        $message = method_exists($exception, 'getMessage') ? $exception->getMessage() : 'Server Error';
      
        return response()->json([
          'success' => false,
          'code' => $statusCode,
          'message' => $message,
          'errors' => [$message],
        ], $statusCode);
        
        return parent::render($request, $exception);
    } 
}
