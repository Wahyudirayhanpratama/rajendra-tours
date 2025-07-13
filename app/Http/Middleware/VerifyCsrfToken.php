<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'api/midtrans/callback',
        '/register',            // dengan slash
        'register',             // tanpa slash (jaga-jaga)
        'http://127.0.0.1:8000/register'
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
