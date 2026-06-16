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

            // Utiliser les vues Blade personnalisées pour les erreurs
            if (in_array($status, [401, 403, 404, 419, 429, 500, 503])) {
                return response()->view("errors.{$status}", ['status' => $status], $status);
            }
        }

        return parent::render($request, $e);
    }
}
