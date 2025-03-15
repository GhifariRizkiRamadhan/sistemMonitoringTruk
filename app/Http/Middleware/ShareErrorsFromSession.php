<?php

namespace Illuminate\Foundation\Http\Middleware;

use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Session;
use Closure;

class ShareErrorsFromSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($errors = Session::get('errors')) {
            // Share errors to view
            view()->share('errors', $errors);
        }

        return $next($request);
    }
}
