<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HandleInertiaFlash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Share the jetstream flash with all Inertia responses
        Inertia::share([
            'jetstream' => [
                'flash' => session('jetstream.flash'),
            ],
        ]);

        return $next($request);
    }
}
