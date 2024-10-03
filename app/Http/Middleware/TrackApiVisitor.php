<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Visit;
use Laravel\Jetstream\Agent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackApiVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $agent = new Agent();

        \App\Models\Visit::create([
            'ip_address' => $request->ip(),
            'endpoint' => $request->path(),
            'device' => $agent->isDesktop() ? 'Desktop' : 'Mobile',
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'visited_at' => now(),
        ]);

        return $next($request);
    }
}
