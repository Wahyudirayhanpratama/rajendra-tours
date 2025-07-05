<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Penumpang;
use App\Models\Pemesanan;
use App\Models\Jadwal;
use App\Models\Tiket;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use Midtrans\Snap;
use App\Services\MidtransService;
use Midtrans\Config;

class PenumpangController extends Controller
{
    //Create dari Pelanggan
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|uuid|exists:jadwals,jadwal_id',
            'total_harga' => 'required|numeric',
            'jumlah_penumpang' => 'required|integer|min:1',
            'nama' => 'required|string',
            'no_hp' => 'required|string',
            'jenis_kelamin' => 'required|string',
            'nomor_kursi' => 'required|array',
            'alamat_jemput' => 'required|string',
            'alamat_antar' => 'required|string',
        ]);
        // dd($request->nomor_kursi);

        DB::beginTransaction();

        try {
            $pemesanan = Pemesanan::create([
                'pemesanan_id' => Str::uuid(),
                'user_id' => Auth::guard('pelanggan')->user()->user_id,
                'jadwal_id' => $request->jadwal_id,
                'jumlah_penumpang' => $request->jumlah_penumpang,
                'total_harga' => $request->total_harga,
                'status' => 'belum_lunas',
                'kode_booking' => 'BK-' . strtoupper(Str::random(6)),
            ]);

            if (count($request->nomor_kursi) !== count(array_unique($request->nomor_kursi))) {
                return back()->with('error', 'Nomor kursi tidak boleh duplikat.');
            }

            Penumpang::create([
                'penumpang_id' => Str::uuid(),
                'pemesanan_id' => $pemesanan->pemesanan_id,
                'nama' => $request->nama,
                'no_hp' => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_kursi' => implode(',', array_unique($request->nomor_kursi)),
                'alamat_jemput' => $request->alamat_jemput,
                'alamat_antar' => $request->alamat_antar,
            ]);

            Tiket::create([
                'tiket_id' => Str::uuid(),
                'pemesanan_id' => $pemesanan->pemesanan_id,
                'no_tiket' => 'TK-' . strtoupper(Str::random(8)),
                'nama_penumpang' => $request->nama,
                'nomor_kursi' => implode(',', $request->nomor_kursi),
            ]);

            DB::commit(); // Jika semuanya berhasil, commit

            // === MIDTRANS SETUP ===
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = false;
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Buat snap token
            $params = [
                'transaction_details' => [
                    'order_id' => $pemesanan->kode_booking,
                    'gross_amount' => $pemesanan->total_harga,
                ],
                'customer_details' => [
                    'first_name' => $request->nama,
                    'phone' => $request->no_hp,
                ],
                'callbacks' => [
                    'finish' => route('tiket'),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

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
                    'nomor_polisi' => $pemesanan->jadwal->mobil->nomor_polisi ?? '-', // pastikan relasi jadwal->mobil tersedia
                    'kode_booking' => $pemesanan->kode_booking,
                    'snap_token' => Snap::getSnapToken($params),
                    'pemesanan_id' => $pemesanan->pemesanan_id,
                ]
            ]);

            // Redirect agar tidak double submit
            return redirect()->route('bayar', ['id' => $pemesanan->pemesanan_id]);
        } catch (\Exception $e) {
            DB::rollback(); // Jika ada yang gagal, rollback semua
            Log::error('Gagal menyimpan penumpang', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['general_error' => 'Gagal menyimpan data penumpang: ' . $e->getMessage()]);
        }
        // dd(request()->all());
    }
    public function showTiket($id)
    {
        $pemesanan = Pemesanan::with('penumpangs', 'jadwal')->findOrFail($id);
        return view('pelanggan.tiket', compact('pemesanan'));
    }

    public function create(Request $request)
    {
        $cityfrom = session('cityfrom');
        $cityto = session('cityto');
        $tanggal = session('tanggal');
        $jumlah_penumpang = session('jumlah_penumpang');
        $jadwal_id = $request->input('jadwal');
        $harga = $request->input('harga');

        $jadwal = Jadwal::with('mobil')->where('jadwal_id', $jadwal_id)->first();

        // Hitung total
        $total_harga = $jadwal->harga * $jumlah_penumpang;
        $kapasitas = $jadwal->mobil->kapasitas;
        $midtrans = new MidtransService();
        $snapToken = $midtrans->createSnapToken(
            'ORDER-' . uniqid(),
            $jadwal->harga, // atau total harga pemesanan sementara
            auth('pelanggan')->user()->nama ?? ''
        );

        // Ambil semua kursi yang sudah dipilih untuk jadwal & mobil ini
        $kursi_db = DB::table('penumpangs')
            ->join('pemesanans', 'penumpangs.pemesanan_id', '=', 'pemesanans.pemesanan_id')
            ->join('jadwals', 'pemesanans.jadwal_id', '=', 'jadwals.jadwal_id')
            ->join('mobils', 'jadwals.mobil_id', '=', 'mobils.mobil_id')
            ->where('pemesanans.jadwal_id', $jadwal_id)
            ->where('mobils.nomor_polisi', $jadwal->mobil->nomor_polisi)
            ->where('pemesanans.status', '!=', 'Tiket dibatalkan') // hanya kursi dari pemesanan aktif
            ->whereNotNull('penumpangs.nomor_kursi')               // hanya yang sudah pilih kursi
            ->pluck('penumpangs.nomor_kursi') // hasil: ['2,4', '5']
            ->toArray();

        // Ubah menjadi array ['2','4','5']
        $kursi_terpakai = [];
        foreach ($kursi_db as $kursi) {
            $kursi_array = explode(',', $kursi);
            $kursi_terpakai = array_merge($kursi_terpakai, array_map('trim', $kursi_array));
        }

        // Simpan ke session
        session([
            'jadwal_id' => $jadwal_id,
            'harga_per_penumpang' => $harga,
            'total_harga' => $total_harga,
            'nomor_polisi' => $jadwal->mobil->nomor_polisi,
            'kapasitas' => $kapasitas,
            'kursi_terpakai' => $kursi_terpakai
        ]);

        return view('pelanggan.data-pemesan', compact(
            'cityfrom',
            'cityto',
            'tanggal',
            'jumlah_penumpang',
            'jadwal_id',
            'total_harga',
            'kapasitas',
            'kursi_terpakai',
            'snapToken'
        ));
    }
    public function index()
    {
        $penumpangs = Penumpang::with(['pemesanan.jadwal.mobil'])->get();
        return view('admin.data-penumpang.penumpang', compact('penumpangs'));
    }
    //Create dari Admin
    public function createPenumpang(Request $request)
    {
        $jadwals = Jadwal::with('mobil')->get();
        $pelanggans = User::where('role', 'pelanggan')->get();

        $pemesanans = Pemesanan::with('penumpangs', 'tiket')
            ->whereHas('penumpangs')
            ->get();

        $kursiTerpakai = [];

        foreach ($pemesanans as $pemesanan) {
            $jadwalId = $pemesanan->jadwal_id;
            $kursi = $pemesanan->penumpangs
                ->flatMap(function ($penumpang) {
                    return explode(',', $penumpang->nomor_kursi);
                })
                ->map(fn($k) => (string) trim($k))
                ->toArray();

            if (!isset($kursiTerpakai[$jadwalId])) {
                $kursiTerpakai[$jadwalId] = [];
            }

            $kursiTerpakai[$jadwalId] = array_merge($kursiTerpakai[$jadwalId], $kursi);
        }

        return view('admin.data-penumpang.tambah-penumpang', compact(
            'pemesanans',
            'pelanggans',
            'jadwals',
            'kursiTerpakai'
        ));
    }

    public function storePenumpang(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid|exists:users,user_id',
            'jadwal_id' => 'required|uuid|exists:jadwals,jadwal_id',
            'jumlah_penumpang' => 'required|integer|min:1',
            'nama' => 'required|string',
            'no_hp' => 'required|string',
            'jenis_kelamin' => 'required|string',
            'nomor_kursi' => 'required|string',
            'alamat_jemput' => 'required|string',
            'alamat_antar' => 'required|string',
        ]);

        $kursiList = array_map('trim', explode(',', $request->nomor_kursi));

        // Hitung jumlah penumpang berdasarkan jumlah kursi yang dipilih
        $jumlahPenumpang = count($kursiList);

        // Validasi jumlah kursi dan duplikasi
        if ($jumlahPenumpang !== count(array_unique($kursiList))) {
            return back()->with('error', 'Nomor kursi tidak boleh duplikat.');
        }

        DB::beginTransaction();

        try {
            // Ambil data jadwal untuk mendapatkan harga per penumpang
            $jadwal = Jadwal::where('jadwal_id', $request->jadwal_id)->firstOrFail();
            $hargaPerPenumpang = $jadwal->harga;
            $totalHarga = $hargaPerPenumpang * intval($request->jumlah_penumpang);

            $pemesanan = Pemesanan::create([
                'pemesanan_id' => Str::uuid(),
                'user_id' => $request->user_id,
                'jadwal_id' => $request->jadwal_id,
                'jumlah_penumpang' => $request->jumlah_penumpang,
                'total_harga' => $totalHarga,
                'status' => 'belum_lunas',
                'kode_booking' => 'BK-' . strtoupper(Str::random(6)),
            ]);

            $pelanggan = User::where('user_id', $request->user_id)->first();

            // Tambahkan data penumpang
            Penumpang::create([
                'penumpang_id' => Str::uuid(),
                'pemesanan_id' => $pemesanan->pemesanan_id,
                'nama' => $pelanggan->nama,
                'no_hp' => $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_kursi' => implode(',', $kursiList),
                'alamat_jemput' => $request->alamat_jemput,
                'alamat_antar' => $request->alamat_antar,
            ]);

            Tiket::create([
                'tiket_id' => Str::uuid(),
                'pemesanan_id' => $pemesanan->pemesanan_id,
                'no_tiket' => 'TK-' . strtoupper(Str::random(8)),
                'nama_penumpang' => $request->nama,
                'nomor_kursi' => implode(',', $kursiList),
            ]);

            DB::commit();
            return redirect()->route('data.penumpang')->with('success', 'Penumpang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menambahkan penumpang', ['error' => $e->getMessage()]);
            return back()->withErrors(['msg' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
        // dd(request()->all());
    }
    // Edit data penumpang tertentu (bisa termasuk jumlah penumpang)
    public function editPenumpang($id)
    {
        $penumpang = Penumpang::with('pemesanan.jadwal.mobil')->findOrFail($id);
        $pemesanan = $penumpang->pemesanan;
        $jadwals = Jadwal::with('mobil')->get(); // jika kamu punya dropdown jadwal
        $jumlah_kursi_mobil = $penumpang->pemesanan->jadwal->mobil->kapasitas ?? 0;
        $currentSeats = array_map('trim', explode(',', $penumpang->nomor_kursi ?? ''));

        // Ambil semua kursi terpakai dari semua jadwal
        $kursiTerpakai = [];
        foreach ($jadwals as $jadwal) {
            $kursiTerpakai[$jadwal->jadwal_id] = Penumpang::whereHas('pemesanan', function ($q) use ($jadwal) {
                $q->where('jadwal_id', $jadwal->jadwal_id);
            })
                ->where('penumpang_id', '!=', $penumpang->penumpang_id) // Tambahkan ini
                ->pluck('nomor_kursi')->flatMap(function ($item) {
                    return explode(',', $item);
                })->map('trim')->toArray();
        }
        return view('admin.data-penumpang.edit-penumpang', compact(
            'penumpang',
            'pemesanan',
            'jadwals',
            'jumlah_kursi_mobil',
            'kursiTerpakai',
            'currentSeats'
        ));
    }

    // Update data penumpang (dengan validasi jumlah penumpang dan waktu H-3 jam)
    public function updatePenumpang(Request $request, $id)
    {
        $penumpang = Penumpang::with('pemesanan.jadwal.mobil')->findOrFail($id);
        $pemesanan = $penumpang->pemesanan;
        $jadwal = $pemesanan->jadwal;
        $mobil = $jadwal->mobil;

        // H-3 jam rule
        $batasWaktu = Carbon::parse($jadwal->tanggal . ' ' . $jadwal->jam_berangkat)->subHours(3);
        if (now()->greaterThan($batasWaktu)) {
            return back()->withErrors(['error' => 'Perubahan hanya dapat dilakukan maksimal 3 jam sebelum keberangkatan.']);
        }

        $request->validate([
            'jumlah_penumpang' => 'required|integer|min:1|max:5',
            'jenis_kelamin' => 'required|in:L,P',
            'nomor_kursi' => 'required|string',
            'alamat_jemput' => 'required|string',
            'alamat_antar' => 'required|string',
        ]);

        // Hitung total kursi terpakai (selain pemesanan ini)
        $kursiTerpakaiLain = Penumpang::whereHas('pemesanan', function ($q) use ($jadwal, $pemesanan) {
            $q->where('jadwal_id', $jadwal->jadwal_id)
                ->where('pemesanan_id', '!=', $pemesanan->pemesanan_id);
        })->pluck('nomor_kursi')->flatMap(function ($item) {
            return explode(',', $item);
        })->toArray();

        $kursiRequest = explode(',', $request->nomor_kursi);
        $kursiRequest = array_map('trim', $kursiRequest);

        if (count(array_intersect($kursiRequest, $kursiTerpakaiLain)) > 0) {
            return back()->withErrors(['nomor_kursi' => 'Beberapa kursi telah terpakai.']);
        }

        if (count($kursiRequest) > ($mobil->kapasitas ?? 0)) {
            return back()->withErrors(['nomor_kursi' => 'Jumlah penumpang melebihi kapasitas mobil.']);
        }

        // Update pemesanan jumlah penumpang
        $pemesanan->jumlah_penumpang = count($kursiRequest);
        $pemesanan->save();

        try {
            // Update penumpang
            $penumpang->update([
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_kursi' => implode(',', $kursiRequest),
                'alamat_jemput' => $request->alamat_jemput,
                'alamat_antar' => $request->alamat_antar,
            ]);

            DB::commit();
            return redirect()->route('data.penumpang')->with('success', 'Data penumpang berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback(); // Jika ada yang gagal, rollback semua
            Log::error('Gagal memperbarui penumpang', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['general_error' => 'Gagal menyimpan data penumpang: ' . $e->getMessage()]);
        }
        // dd(request()->all());
    }
    public function destroyPenumpang($id)
    {
        $penumpang = Penumpang::findOrFail($id);
        $pemesanan_id = $penumpang->pemesanan_id;

        $penumpang->delete();

        // Cek apakah masih ada penumpang lain dengan pemesanan_id yang sama
        $jumlahPenumpangLain = Penumpang::where('pemesanan_id', $pemesanan_id)->count();

        if ($jumlahPenumpangLain === 0) {
            // Hapus pemesanan jika tidak ada penumpang lagi
            Pemesanan::where('pemesanan_id', $pemesanan_id)->delete();
        }

        return redirect()->route('data.penumpang')->with('success', 'Penumpang dihapus');
    }
}
