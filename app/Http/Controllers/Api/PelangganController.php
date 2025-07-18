<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    public function dataPelanggan(Request $request)
    {
        $pelanggan = User::where('role', 'pelanggan')->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $pelanggan
            ]);
        }
        return view('admin.data-akun.data-pelanggan', compact('pelanggan'));
    }

    // Tambah pelanggan (form)
    public function createPelanggan()
    {
        return view('admin.data-akun.tambah-data-pelanggan');
    }

    // Simpan pelanggan baru
    public function storePelanggan(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'no_hp' => 'required',
            'alamat' => 'required',
            'password' => 'required',
        ]);

        $pelanggan = User::create([
            'user_id' => Str::uuid(),
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'password' => bcrypt($request->password),
            'role' => 'pelanggan',
        ]);
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan.',
                'pelanggan' => $pelanggan
            ], 201);
        }

        return redirect()->route('data-pelanggan')->with('success', 'Data pelanggan berhasil ditambahkan');
    }

    // Hapus pelanggan
    public function deletePelanggan(Request $request, $id)
    {
        User::where('user_id', $id)->delete();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil dihapus.',
                'mobil_id' => $id
            ], 200);
        }
        return redirect()->route('data-pelanggan')->with('success', 'Pelanggan berhasil dihapus');
    }

    // (opsional) Edit pelanggan
    public function editPelanggan($id)
    {
        $pelanggan = User::where('user_id', $id)->firstOrFail();
        return view('admin.data-akun.edit-data-pelanggan', compact('pelanggan'));
    }

    public function updatePelanggan(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'no_hp' => 'required',
            'alamat' => 'required',
        ]);

        User::where('user_id', $id)->update([
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Pelanggan berhasil diperbarui.',
                'pelanggan' => $id
            ], 200);
        }

        return redirect()->route('data-pelanggan')->with('success', 'Data berhasil diperbarui');
    }
}
