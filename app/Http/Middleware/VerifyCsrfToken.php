<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'api/midtrans/callback',
        // Mobil
        '/mobil/tambah',
        '/mobil/update/*',
        '/mobil/hapus/*',
    ];
    protected function tokensMatch($request)
    {
        Log::info('CSRF check on: ' . $request->path());

        if ($this->inExceptArray($request)) {
            Log::info('✅ Dikecualikan CSRF: ' . $request->path());
            return true;
        }

        return parent::tokensMatch($request);
    }
}
