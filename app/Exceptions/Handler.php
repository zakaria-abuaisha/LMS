<?php

namespace App\Exceptions;

use App\Traits\ApiResponses;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponses;

    protected $handlers = [
        ValidationException::class => 'handleValidation',
        NotFoundHttpException::class => 'handleNotFound',
        AuthenticationException::class => 'handleAuthentication',
        AuthorizationException::class => 'handleAuthorization',
    ];

    private function handleNotFound()
    {
        return [
            [
                "status" => 404,
                "message" => "The Resource Could Not Be Found :("
            ]
        ];
    }

    private function handleValidation(ValidationException $exception)
    {
        foreach ($exception->errors() as $key => $value) 
        {
            foreach ($value as $message)
            {
                $errors[] = [
                    "status" => 422,
                    "message" => $message,
                    "source" => $key
                ];
            }
        }
        return $errors;
    }

    private function handleAuthorization(AuthorizationException $exception)
    {

        return [
            [
                "status" => 403,
                "message" => $exception->getMessage(),
                "source" => $exception->getFile()
            ]
        ];
    }

    private function handleAuthentication()
    {
        return [
            [
                'status' => 401,
                'message' => 'You are NOT Authenticated',
            ]
        ];
    }

    public function render($request, Throwable $exception)
    {
        $className = get_class($exception);

        if (array_key_exists($className, $this->handlers))
        {
            $method = $this->handlers[$className];
            return $this->error($this->$method($exception));
        }

        $index = strrpos($className, '\\');

        return $this->error([
            "type" => substr($className, $index + 1),
            "status" => 0,
            "message" => $exception->getMessage(),
            "source" => "Line: " . $exception->getLine() . ": ". $exception->getFile(),
        ]);
    }
}