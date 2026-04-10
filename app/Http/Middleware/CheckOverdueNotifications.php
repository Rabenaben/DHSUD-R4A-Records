<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOverdueNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($user && $user->role === 'Admin') {
            Notification::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => 'overdue_notices'
                ],
                [
                    'read_at' => null
                ]
            );
        }

        return $next($request);
    }
}
