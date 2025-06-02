<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        $userRole = strtolower(auth()->user()->role);
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json(['message' => 'Unauthorized. Access denied for your role.'], 403);
        }

        return $next($request);
    }
}
