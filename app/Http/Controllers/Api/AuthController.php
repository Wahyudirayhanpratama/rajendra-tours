<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginAdminPemilikApi(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('nama', $request->username)
            ->whereIn('role', ['admin', 'pemilik'])
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Username atau password salah.'], 401);
        }

        // Hapus token lama (opsional)
        $user->tokens()->delete();

        $token = $user->createToken($user->role . '-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil!',
            'token' => $token,
            'user' => [
                'nama' => $user->nama,
                'role' => $user->role,
            ]
        ]);
    }
    public function loginPelangganApi(Request $request)
    {
        $request->validate([
            'no_hp' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('no_hp', $request->no_hp)
            ->where('role', 'pelanggan')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Nomor HP atau password salah.'], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('pelanggan-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil!',
            'token' => $token,
            'user' => [
                'nama' => $user->nama,
                'no_hp' => $user->no_hp,
                'role' => $user->role,
            ]
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('nama', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            // Logout guard lain terlebih dahulu
            Auth::guard('admin')->logout();
            Auth::guard('pemilik')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                Auth::guard('admin')->login($user); // aktifkan guard admin
                $request->session()->regenerate();
                return response()->json([
                    'message' => 'Login berhasil!',
                    'user' => [
                        'username' => $user->nama,
                        'role' => $user->role,
                    ],
                ], 200);
            }

            if ($user->role === 'admin') {
                Auth::guard('admin')->login($user);
                $request->session()->regenerate();
                return redirect()->route('dashboard.admin');
            } elseif ($user->role === 'pemilik') {
                Auth::guard('pemilik')->login($user);
                $request->session()->regenerate();
                return redirect()->route('dashboard.pemilik');
            } else {
                return back()->withErrors(['username' => 'Role tidak dikenali.']);
            }
        }
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Username atau password salah.'
            ], 401);
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    // Pelanggan login (form)
    public function loginPelanggan(Request $request)
    {
        $request->validate([
            'no_hp' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('no_hp', $request->no_hp)
            ->where('role', 'pelanggan') // memastikan hanya role pelanggan
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::guard('pelanggan')->login($user); // gunakan guard pelanggan

            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Login berhasil!',
                    'user' => [
                        'nama' => $user->nama,
                        'no_hp' => $user->no_hp,
                        'role' => $user->role,
                    ],
                ], 200);
            }

            return redirect()->route('cari-jadwal');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Nomor HP atau password salah.'
            ], 401);
        }

        return back()->withErrors([
            'no_hp' => 'Nomor HP atau password salah.',
        ]);
    }

    public function showProfil()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login.pelanggan');
        }

        // Get authenticated user
        $user = Auth::user();

        // Verify the user is a pelanggan
        if ($user->role !== 'pelanggan') {
            Auth::logout();
            return redirect()->route('login.pelanggan');
        }

        return view('profil', compact('user'));
    }

    // AJAX login for pelanggan
    public function loginAjax(Request $request)
    {
        // Validasi input
        $request->validate([
            'no_hp' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('no_hp', 'password');

        if (Auth::guard('pelanggan')->attempt($credentials)) {
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Login gagal. Periksa kembali nomor HP dan password.',
        ], 401);
    }

    // Common logout
    public function logoutPelanggan(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect based on user type
        if (Auth::guard('pelanggan')->check()) {
            return redirect()->route('cari-jadwal')->with('show_login_modal', true);
        }
        return redirect()->route('login.pelanggan');
    }

    // Show pelanggan login form
    public function showPelangganLoginForm()
    {
        return view('auth.login-pelanggan');
    }
    public function showPelangganRegisterForm()
    {
        return view('auth.registrasi');
    }
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    public function logoutApi(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil!'
        ]);
    }
}
