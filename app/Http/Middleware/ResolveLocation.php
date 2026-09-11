<?php

namespace App\Http\Middleware;

use App\Models\Location;
use App\Services\LocationContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveLocation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $locationId = session('current_location_id');
        if (!$locationId) {
            $locationId = Location::where('type', 'branch')->first()?->id;
        }

        if (!$locationId) return $next($request);

        $location = Location::find($locationId);
        app(LocationContext::class)->set($location);

        return $next($request);
    }
}
