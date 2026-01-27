<?php

namespace App\Http\Middleware;

use App\Models\Datalog as ModelsDatalog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Datalog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->logAction($request, 'AUTHORIZATION_FAILURE', [
                'message' => $e->getMessage(),
                'url' => $request->fullUrl(),
            ]);
            throw $e;
        }

        if ($request->method() !== 'GET') {
            $this->logAction($request, $request->path());
        }

        return $response;
    }

    private function logAction(Request $request, string $action, array $extraData = []): void
    {
        $data = new ModelsDatalog();
        $data->action = $action;
        $data->user_id = $request->user()->id ?? null;
        $data->data = json_encode(array_merge($request->all(), $extraData));
        $data->save();
    }
}
