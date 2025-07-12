<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Pemesanan;
use App\Models\Penumpang;
use App\Models\User;
use App\Models\Jadwal;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\DB;


class PemesananController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'jadwal_id' => 'required|uuid|exists:jadwals,jadwal_id',
            'jumlah_penumpang' => 'required|integer|min:1',
            'total_harga' => 'required|numeric|min:0',
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'nomor_kursi' => 'required|array|min:1',
            'nomor_kursi.*' => 'string|max:10',
            'alamat_jemput' => 'nullable|string',
            'alamat_antar' => 'nullable|string',
        ]);

        // Simpan data pemesanan
        DB::beginTransaction();
        try {
            // Simpan data pemesanan
            $pemesanan = Pemesanan::create([
                'pemesanan_id' => Str::uuid(),
                'user_id' => Auth::id(),
                'jadwal_id' => $request->jadwal_id,
                'jumlah_penumpang' => $request->jumlah_penumpang,
                'total_harga' => $request->total_harga,
                'status' => 'belum_lunas', // Status awal: belum lunas
                'kode_booking' => 'BK-' . strtoupper(Str::random(6)), // Generate kode booking di sini
                'transaction_id' => null,
                'transaction_time' => null,
                'payment_type' => null,
                'va_number' => null,
                'gross_amount' => null,
            ]);

            // Simpan data penumpang (satu penumpang saja)
            foreach ($request->nomor_kursi as $kursi) {
                Penumpang::create([
                    'penumpang_id' => Str::uuid(),
                    'pemesanan_id' => $pemesanan->pemesanan_id,
                    'nama' => $request->nama,
                    'no_hp' => $request->no_hp,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'nomor_kursi' => $kursi,
                    'alamat_jemput' => $request->alamat_jemput,
                    'alamat_antar' => $request->alamat_antar,
                ]);
            }

            DB::commit(); // Commit transaksi

            // Redirect ke method di PembayaranController untuk inisiasi pembayaran
            // Teruskan ID pemesanan yang baru dibuat
            return redirect()->route('pembayaran.show', ['pemesanan_id' => $pemesanan->pemesanan_id]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback jika ada error
            Log::error('Error creating pemesanan: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->withErrors('Terjadi kesalahan saat membuat pemesanan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pemesanan = Pemesanan::with('penumpangs')->findOrFail($id);
        return view('pelanggan.tiket', compact('pemesanan'));
    }
    public function create(Request $request)
    {
        $cityfrom = session('cityfrom');
        $cityto = session('cityto');
        $tanggal = session('tanggal');
        $jumlah_penumpang = session('jumlah_penumpang');
        $jadwal_id = $request->input('jadwal_id');

        return view('pelanggan.data-pemesan', compact('cityfrom', 'cityto', 'tanggal', 'jumlah_penumpang', 'jadwal_id'));
    }
    public function pemesanan()
    {
        $pemesanans = Pemesanan::with(['jadwal.mobil', 'penumpangs', 'tiket'])->latest()->get();
        return view('admin.data-pemesanan.data-pemesanan', compact('pemesanans'));
    }
    public function storePemesanan(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,jadwal_id',
            'jumlah_penumpang' => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();
        $jadwal = Jadwal::findOrFail($request->jadwal_id);
        $total_harga = $jadwal->harga * $request->jumlah_penumpang;

        DB::beginTransaction();
        try {
            $pemesanan = Pemesanan::create([
                'pemesanan_id' => Str::uuid(),
                'user_id' => $user->id,
                'jadwal_id' => $jadwal->jadwal_id,
                'jumlah_penumpang' => $request->jumlah_penumpang,
                'total_harga' => $total_harga,
                'status' => 'belum_lunas',
                'kode_booking' => 'BK-' . strtoupper(Str::random(6)),
                'transaction_id' => null,
                'transaction_time' => null,
                'payment_type' => null,
                'va_number' => null,
                'gross_amount' => null,
            ]);

            DB::commit();

            // Redirect ke method di PembayaranController untuk inisiasi pembayaran
            return redirect()->route('pembayaran.show', ['pemesanan_id' => $pemesanan->pemesanan_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pemesanan (storePemesanan): ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->withErrors('Terjadi kesalahan saat membuat pemesanan: ' . $e->getMessage());
        }
    }
    public function bayarTiket($id)
    {
        $pemesanan = Pemesanan::with('jadwal.mobil')->findOrFail($id);
        $snapToken = session('preview_pemesanan.snap_token');

        return view('pelanggan.bayar', compact('pemesanan', 'snapToken'));
    }
    public function cetakNota($id)
    {
        $pemesanan = Pemesanan::with([
            'penumpangs',
            'jadwal.mobil',
            'jadwal'
        ])->findOrFail($id);

        return view('admin.data-pemesanan.cetak-nota', compact('pemesanan'));
    }
}
