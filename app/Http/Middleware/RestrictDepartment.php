<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictDepartment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $department): Response
    {
        $deps = explode(',', $department);
        if ($request->user()?->department_id != $department) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
