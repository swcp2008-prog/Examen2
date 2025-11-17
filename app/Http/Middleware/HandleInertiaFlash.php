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
        // Get current flash from session
        $flash = session('jetstream.flash');
        \Log::info('[HandleInertiaFlash] Flash from session:', ['flash' => $flash]);
        
        // Share the jetstream flash with all Inertia responses
        Inertia::share([
            'jetstream' => [
                'flash' => $flash,
            ],
        ]);

        return $next($request);
    }
}
