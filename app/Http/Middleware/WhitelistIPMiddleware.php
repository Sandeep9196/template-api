<?php

namespace App\Http\Middleware;

use App\Models\WhitelistIP;
use Closure;
use Illuminate\Http\Request;

class WhitelistIPMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $whitelistIps = WhitelistIP::where('status', true)->pluck('address')->toArray();

        // If no whitelist ip
        if (! count($whitelistIps)) {
            return $next($request);
        }

        // If the ip is matched, return true
        if (in_array($request->ip(), $whitelistIps)) {
            return $next($request);
        }

        foreach ($whitelistIps as $ip) {
            $wildcardPos = strpos($ip, '*');

            // Check if the ip has a wildcard
            if ($wildcardPos !== false && substr($request->ip(), 0, $wildcardPos) == substr($ip, 0, $wildcardPos)) {
                return $next($request);
            }
        }

        return response()->json([
            'messages' => ['Your current IP Address is restricted to access the System. Please contact the administrator'],
        ], 403);
    }
}
