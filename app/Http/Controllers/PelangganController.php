<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    public function dataPelanggan()
    {
        $pelanggan = User::where('role', 'pelanggan')->latest()->paginate(5);
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

        User::create([
            'user_id' => Str::uuid(),
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'password' => bcrypt($request->password),
            'role' => 'pelanggan',
        ]);

        return redirect()->route('data-pelanggan')->with('success', 'Data pelanggan berhasil ditambahkan');
    }

    // Hapus pelanggan
    public function deletePelanggan($id)
    {
        User::where('user_id', $id)->delete();
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

        return redirect()->route('data-pelanggan')->with('success', 'Data berhasil diperbarui');
    }
}
