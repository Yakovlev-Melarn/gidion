<?php

namespace App\Http\Middleware;

use App\Models\ReactSession;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->headers->get('authorization');
        if (!$user = User::where("token", $token)->first()) {
            return response()->json(['error' => 'unauthorized'], 401);
        }
        session()->put('auth', $user->id);
        if (!ReactSession::where("userId", $user->id)->where("key", 'auth')->count()) {
            $session = new ReactSession();
            $session->userId = $user->id;
            $session->key = 'auth';
            $session->value = $user->id;
            $session->save();
        }
        return $next($request);
    }
}
