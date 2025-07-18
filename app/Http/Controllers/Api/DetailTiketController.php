<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemesanan;
use Carbon\Carbon;

class DetailTiketController extends Controller
{
    public function index()
    {
        $user = Auth::guard('pelanggan')->user();
        return view('pelanggan.detail-tiket', compact('user'));
    }
    public function batalkan($id)
    {
        $pemesanan = Pemesanan::with(['jadwal', 'penumpangs'])->where('pemesanan_id', $id)->firstOrFail();

        if (!$pemesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pemesanan tidak ditemukan.'
            ], 404);
        }
        // Cek status sudah dibatalkan atau belum
        if ($pemesanan->status === 'Tiket dibatalkan') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket sudah dibatalkan sebelumnya.'
            ], 422);
            // return back()->with('error', 'Tiket sudah dibatalkan sebelumnya.');
        }

        // Cek apakah masih bisa dibatalkan (maksimal H-3 jam)
        $waktuKeberangkatan = Carbon::parse($pemesanan->jadwal->tanggal . ' ' . $pemesanan->jadwal->jam_berangkat);
        $batasPembatalan = $waktuKeberangkatan->subHours(3);

        if (now()->greaterThanOrEqualTo($batasPembatalan)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket hanya bisa dibatalkan maksimal 3 jam sebelum keberangkatan.'
            ], 403);
            // return back()->with('error', 'Tiket hanya bisa dibatalkan maksimal 3 jam sebelum keberangkatan.');
        }

        // Update status menjadi dibatalkan
        $pemesanan->update([
            'status' => 'Tiket dibatalkan',
        ]);

        // Kosongkan nomor kursi penumpang agar bisa digunakan kembali
        foreach ($pemesanan->penumpangs as $penumpang) {
            $penumpang->update(['nomor_kursi' => null]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Tiket berhasil dibatalkan dan kursi dikembalikan.',
            'data' => $pemesanan
        ]);

        // return redirect()->route('detail.tiket', $id)->with(
        //     'success',
        //     'Tiket berhasil dibatalkan dan kursi dikembalikan.'
        // );
    }
}
