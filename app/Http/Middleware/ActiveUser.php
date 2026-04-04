<?php

namespace App\Http\Middleware;

use App\Enums\Status;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) return abort(403);

        if ($user->status != Status::active) return abort(403, "This user is not allowed to access the application. Contact IT.");

        return $next($request);
    }
}
