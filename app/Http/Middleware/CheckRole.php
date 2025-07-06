<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
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
        $roles = $this->CheckRoute($request->route());

        if($request->user()->hasRole($roles) || !$roles) {
            return $next($request);
        }

        abort(403, 'you do not have access rights');
    }

    private function CheckRoute($route)
    {
        $actions = $route->getAction();
        return $actions['roles'] ?? null;
    }
}
