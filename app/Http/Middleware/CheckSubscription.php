<?php

namespace App\Http\Middleware;

use App\Facades\Acl;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Acl::hasRole(['admin', 'super-admin'])) {
            return $next($request);
        }

        $user = Auth::user();
        if (!$user || !$user->isSubscriber()) {
            return to_route('profile.wallet')->with('no-subscriber-alert', 'برای دسترسی به این بخش باید اشتراک فعال داشته باشید.');
        }

        return $next($request);
    }
}
