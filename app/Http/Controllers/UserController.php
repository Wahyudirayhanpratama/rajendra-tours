<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Tampilkan semua user
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Tampilkan form tambah user
    public function create()
    {
        return view('admin.users.create');
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'password' => 'required|string|min:6',
            'role' => 'required|string'
        ]);

        User::create([
            'user_id' => Str::uuid(),
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    // Tampilkan detail user
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    // Tampilkan form edit user
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // Update data user
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'role' => 'required|string'
        ]);

        $user->update([
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'role' => $request->role,
        ]);

        // Update password jika ada input baru
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    // Hapus user
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function registerPelanggan(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'no_hp' => 'required',
            'alamat' => 'required',
            'password' => 'required',
        ]);

        $user = User::create([
            'user_id' => Str::uuid(),
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'password' => bcrypt($request->password),
            'role' => 'pelanggan',
        ]);

        if (session('from') == 'data-pemesan') {
            session()->forget('from'); // jangan lupa bersihkan agar tidak looping
            return redirect()->route('penumpang.create');
        }

        return redirect()->route('login.pelanggan')->with('success', 'Registrasi berhasil! Silakan login.');
    }
    public function redirectToRegister(Request $request)
    {
        if ($request->from == 'data-pemesan') {
            // Simpan semua data penting ke session
            session([
                'jadwal_id' => $request->jadwal_id,
                'cityfrom' => $request->cityfrom,
                'cityto' => $request->cityto,
                'tanggal' => $request->tanggal,
                'jumlah_penumpang' => $request->jumlah_penumpang,
                'from' => 'data-pemesan',
                'show_login_popup' => true
            ]);
        }

        return redirect()->route('register.pelanggan');
    }
}
