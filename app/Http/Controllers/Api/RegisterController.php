<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'user_id' => Str::uuid(),
            'nama' => $request->name,
            'no_hp' => $request->phone,
            'alamat' => $request->address,
            'password' => Hash::make($request->password),
            'role' => 'pelanggan', // default untuk yang daftar adalah pelanggan
        ]);

        return redirect('/login-pelanggan')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}
