<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($this->isHttpException($e)) {
            $status = $e->getStatusCode();

            if ($request->expectsJson()) {
                return parent::render($request, $e);
            }

            if (in_array($status, [401, 403, 404, 500, 503])) {
                return Inertia::render('Error', [
                    'status' => $status,
                ]);
            }
        }

        return parent::render($request, $e);
    }
}
