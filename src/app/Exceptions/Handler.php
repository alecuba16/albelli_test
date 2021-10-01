<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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

    //alecuba16
    /**
     * Render an exception into an HTTP response.
     * Updated to return json for a request that wantsJson
     * i.e: specifies
     *      Accept: application/json
     * in its header
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $details = $this->details($e);
            return response()->json(
                [
                    'success' => 'false',
                    'message' => $details['message'],
                    'data' => null
                ],
                $details['statusCode']
            );
        }
        return parent::render($request, $e);
    }

    protected function details(Throwable $e) : array
    {
        // We will give Error 500 if we cannot detect the error from the exception
        $statusCode = 500;
        $message = $e->getMessage();

        if (method_exists($e, 'getStatusCode')) { // Not all Exceptions have a http status code
            $statusCode = $e->getStatusCode();
        }

        if($e instanceof ModelNotFoundException) {
            $statusCode = 404;
        }
        else if($e instanceof QueryException) {
            $statusCode = 406;
            $integrityConstraintViolation = 1451;
            if ($e->errorInfo[1] == $integrityConstraintViolation) {
                $message = "Cannot proceed with query, it is referenced by other records in the database.";
                \Log::info($e->errorInfo[2]);
            }
            else {
                $message = 'Could not execute query: ' . $e->errorInfo[2];
                \Log::error($message);
            }
        }
        elseif ($e instanceof NotFoundHttpException) {
            $message = "Url does not exist.";
        } else if($e instanceof AuthenticationException) {
            $statusCode = 401;
            $message = "User not authenticated.";
        }

        return compact('statusCode', 'message');
    }
}
