<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class BearerTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Bearer token required'], 401);
        }

        $token = substr($authHeader, 7);


        $user = User::where('bearer_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }


        // mount user
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
