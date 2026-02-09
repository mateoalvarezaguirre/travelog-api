<?php

namespace App\Http\Middlewares;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelescopeAuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Closure|JsonResponse|Response
    {
        $username = $request->header('PHP_AUTH_USER');
        $password = $request->header('PHP_AUTH_PW');

        if (app()->environment('local') || $this->isAuthCorrect($username, $password)) {
            return $next($request);
        }

        return response()->make('Invalid credentials.', 401, ['WWW-Authenticate' => 'Basic']);
    }

    private function isAuthCorrect(?string $username, ?string $password): bool
    {
        return $username === config('telescope.basic_auth.username')
            && $password === config('telescope.basic_auth.password');
    }
}
