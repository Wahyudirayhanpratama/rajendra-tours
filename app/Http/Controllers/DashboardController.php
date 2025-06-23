<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mobil;
use App\Models\User;
use App\Models\Pemesanan;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahMobil = Mobil::count(); // Menghitung total mobil

        return view('dashboard.index', compact('jumlahMobil'));
    }
    public function dashboard()
    {
        $jumlahMobil = Mobil::count(); // Hitung jumlah mobil
        $jumlahPelanggan = User::where('role', 'pelanggan')->count(); // Hitung jumlah pelanggan
        $pembatalanBulanIni = Pemesanan::where('status', 'Tiket dibatalkan')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();

        $pemesananBulanIni = Pemesanan::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Ambil semua jadwal hari ini
        $jadwalHariIni = Jadwal::with(['mobil', 'pemesanan' => function ($query) {
            $query->where('status', 'lunas');
        }])->whereDate('tanggal', Carbon::today())
            ->get();

        return view('admin.dashboard.dashboard', compact(
            'jumlahMobil',
            'jumlahPelanggan',
            'pembatalanBulanIni',
            'pemesananBulanIni',
            'jadwalHariIni'
        ));
    }
    public function dashboardPemilik()
    {
        // Total tiket lunas bulan ini
        $totalTiketBulanIni = Pemesanan::where('status', 'lunas')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Total pembatalan tiket bulan ini
        $totalPembatalanBulanIni = Pemesanan::where('status', 'Tiket dibatalkan')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();

        // Total pendapatan bulan ini
        $pendapatanBulanan = Pemesanan::where('status', 'lunas')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_harga');

        // Total pendapatan tahun ini
        $pendapatanTahunan = Pemesanan::where('status', 'lunas')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_harga');

        // Tiket per bulan (1-12)
        $grafikBulanan = Pemesanan::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->where('status', 'lunas')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'bulan');

        // Ubah ke array 1-12, isi 0 jika tidak ada data
        $dataGrafik = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataGrafik[] = $grafikBulanan[$i] ?? 0;
        }

        // Ambil pemesanan bulan ini dengan relasi ke jadwal
        $pemesanans = Pemesanan::with('jadwal')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', '!=', 'Tiket dibatalkan')
            ->get();

        // Hitung jumlah tiket per kota tujuan
        $ruteCounts = [];
        foreach ($pemesanans as $pemesanan) {
            $kota = $pemesanan->jadwal->kota_tujuan ?? 'Tidak diketahui';
            if (!isset($ruteCounts[$kota])) {
                $ruteCounts[$kota] = 0;
            }
            $ruteCounts[$kota] += $pemesanan->jumlah_penumpang;
        }

        // Format untuk grafik donut (JS)
        $colors = ['#3c8dbc', '#0073b7', '#00c0ef', '#f39c12', '#00a65a', '#dd4b39'];
        $donutData = [];
        $index = 0;
        foreach ($ruteCounts as $label => $data) {
            $donutData[] = [
                'label' => $label,
                'data' => $data,
                'color' => $colors[$index % count($colors)]
            ];
            $index++;
        }

        $bulan = Carbon::now()->format('m');
        $tahun = Carbon::now()->format('Y');

        // Ambil jumlah pemesanan per hari di bulan ini
        $aktivitasHarian = DB::table('pemesanans')
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get()
            ->map(function ($item) {
                return [
                    'label' => formatHariTanggalPendek($item->tanggal), // contoh: "Jum, 21"
                    'total' => $item->total
                ];
            });

        return view('pemilik.dashboard.dashboard-pemilik', array_merge(
            compact(
                'totalTiketBulanIni',
                'totalPembatalanBulanIni',
                'pendapatanBulanan',
                'pendapatanTahunan',
                'dataGrafik'
            ),
            ['donutData' => json_encode($donutData)],
            ['aktivitasHarian' => $aktivitasHarian]
        ));
    }
}
