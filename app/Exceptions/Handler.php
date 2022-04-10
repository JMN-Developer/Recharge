<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use BadMethodCallException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use InvalidArgumentException;
use Error;
use ErrorException;
use Illuminate\Database\QueryException;



class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // if ($exception instanceof NotFoundHttpException || $exception instanceof MethodNotAllowedHttpException || $exception instanceof ModelNotFoundException || $exception instanceof InvalidArgumentException || $exception instanceof ErrorException || $exception instanceof QueryException || $exception instanceof Error ||$exception instanceof BadMethodCallException ) {

        //     return response()->view('error.index', [], 500);
        // }
        return parent::render($request, $exception);
        //return response()->view('error.index', [], 500);
    }

}
