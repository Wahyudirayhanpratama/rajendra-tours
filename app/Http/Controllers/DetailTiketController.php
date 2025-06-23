<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemesanan;


class DetailTiketController extends Controller
{
    public function index()
    {
        $user = Auth::guard('pelanggan')->user();
        return view('pelanggan.detail-tiket', compact('user'));
    }
    public function batalkan($id)
    {
        $pemesanan = Pemesanan::where('pemesanan_id', $id)->firstOrFail();

        if ($pemesanan->status === 'Tiket dibatalkan') {
            return back()->with('error', 'Tiket sudah dibatalkan sebelumnya.');
        }
        
        $pemesanan->update([
            'status' => 'Tiket dibatalkan',
        ]);


        return redirect()->route('detail.show', $id)->with('success', 'Tiket berhasil dibatalkan.');
    }
}
