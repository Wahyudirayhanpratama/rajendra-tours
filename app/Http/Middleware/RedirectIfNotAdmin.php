<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAdmin
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai admin terlebih dahulu.');
        }

        $user = Auth::guard('admin')->user();
        if ($user->role !== 'admin') {
            Auth::guard('admin')->logout();
            return redirect()->route('login')->with('error', 'Akses ditolak. Bukan admin.');
        }

        return $next($request);
    }
}
