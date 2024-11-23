<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        // \League\OAuth2\Server\Exception\OAuthServerException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
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

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*') && $e instanceof AuthenticationException) {
            $message = "Unauthorised";
            return $this->sendFailResponse(__('api_messages.passport_unauthorised'), 401);
        }
        if ($this->isHttpException($e)) {
            if ($e->getStatusCode() == 404) {
                return response()->view('errors.404', [], 404);
            }
            if ($e->getStatusCode() == 500) {
                return response()->view('errors.500', [], 500);
            }
        }
        return parent::render($request, $e);
    }

    public function report(Throwable $e)
    {
        // Kill reporting if this is an "access denied" (code 9) OAuthServerException.
        if ($e instanceof \League\OAuth2\Server\Exception\OAuthServerException && $e->getCode() == 9) {

            return $this->sendFailResponse($e->getMessage(), 500);
        }
        parent::report($e);
    }
}