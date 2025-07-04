<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Jadwal;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;
use App\Services\MidtransService;

class PembayaranController extends Controller
{
    public function preview(Request $request, MidtransService $midtrans)
    {
        // Validasi data minimal dulu (opsional)
        $request->validate([
            'jadwal_id' => 'required',
            'jumlah_penumpang' => 'required|numeric',
            'total_harga' => 'required|numeric',
            'nama' => 'required|string',
            'no_hp' => 'required|string',
            'jenis_kelamin' => 'required',
            'nomor_kursi' => 'required|array',
            'alamat_jemput' => 'required|string',
            'alamat_antar' => 'required|string',
        ]);

        // Ambil data dari jadwal
        $jadwal = Jadwal::with('mobil')->where('jadwal_id', $request->jadwal_id)->firstOrFail();

        // Simpan sementara ke session
        session([
            'preview_pemesanan' => [
                'cityfrom' => session('cityfrom'),
                'cityto' => session('cityto'),
                'tanggal' => session('tanggal'),
                'jumlah_penumpang' => $request->jumlah_penumpang,
                'total_harga' => $request->total_harga,
                'jadwal_id' => $request->jadwal_id,
                'nama' => $request->nama,
                'no_hp' => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_kursi' => implode(',', $request->nomor_kursi),
                'alamat_jemput' => $request->alamat_jemput,
                'alamat_antar' => $request->alamat_antar,
                'nomor_polisi' => $jadwal->mobil->nomor_polisi,
                'kode_booking' => $request->kode_booking,
            ]
        ]);

        $kode_booking = 'BK-' . strtoupper(Str::random(6));

        // Tambahkan ini di atas:
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $payload = [
            'transaction_details' => [
                'order_id' => $kode_booking,
                'gross_amount' => $request->total_harga,
            ],
            'customer_details' => [
                'first_name' => $request->nama,
                'email' => Auth::guard('pelanggan')->user()->email ?? 'dummy@mail.com',
            ]
        ];

        $snapToken = $midtrans->createSnapTransaction($payload);

        return view('pelanggan.bayar', [
            'cityfrom' => session('cityfrom'),
            'cityto' => session('cityto'),
            'tanggal' => session('tanggal'),
            'jumlah_penumpang' => $request->jumlah_penumpang,
            'total_harga' => $request->total_harga,
            'kode_booking' => $kode_booking,
            'jadwal_id' => $request->jadwal_id,
            'snapToken' => $snapToken,
        ]);
    }
}
