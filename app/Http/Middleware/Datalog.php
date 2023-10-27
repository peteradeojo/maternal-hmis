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
        $routeList = [];

        if (!$request->method() !== 'GET') {
            $path = url($request->path());

            $data = new ModelsDatalog();

            $data->action = $routeList[$path] ?? $request->path();
            $data->user_id = $request->user()->id ?? "API";
            $data->data = json_encode($request->all());

            $data->save();
        }
        return $next($request);
    }
}
