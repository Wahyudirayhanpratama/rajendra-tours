<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotPemilik
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('pemilik')->check()) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai pemilik terlebih dahulu.');
        }

        $user = Auth::guard('pemilik')->user();
        if ($user->role !== 'pemilik') {
            Auth::guard('pemilik')->logout();
            return redirect()->route('login')->with('error', 'Akses ditolak. Bukan pemilik.');
        }

        return $next($request);
    }
}
