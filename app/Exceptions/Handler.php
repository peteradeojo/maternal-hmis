<?php

namespace App\Exceptions;

use App\Enums\Queues;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
            $message = $e->getMessage() . "@" . $e->getFile() . ":" . $e->getLine();
            $context = $this->context() + [
                'stack' => $e->getTraceAsString(),
            ];
            dispatch(function () use (&$message, &$context) {
                try {
                    laas()->emergency($message, $context);
                } catch (Throwable $e) {
                    logger()->emergency($message, $context);
                    logger()->emergency($e->getMessage());
                }
            });
        });
    }
}
