<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotPelanggan
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('pelanggan')->check()) {
            return redirect()->route('login.pelanggan')->with('error', 'Silakan login terlebih dahulu.'); // pastikan ini route pelanggan
        }
        $user = Auth::guard('pelanggan')->user();
        if ($user->role !== 'pelanggan') {
            Auth::guard('pelanggan')->logout();
            return redirect()->route('login.pelanggan');
        }

        return $next($request);
    }
}
