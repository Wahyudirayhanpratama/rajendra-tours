<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemesanan;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index()
    {
        $userId = Auth::guard('pelanggan')->user()->user_id;

        $riwayatTiket = Pemesanan::with(['jadwal', 'tiket'])
            ->where('user_id', $userId)
            ->whereIn('status', ['Lunas', 'Tiket dibatalkan'])
            ->with(['jadwal'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($pemesanan) {
                if (!$pemesanan->jadwal) return false;

                // Gabungkan tanggal dan jam_berangkat jadi satu timestamp
                $datetime = $pemesanan->jadwal->tanggal . ' ' . $pemesanan->jadwal->jam_berangkat;
                return strtotime($datetime) < now()->timestamp;
            });

        return view('pelanggan.riwayat', compact('riwayatTiket'));
    }
}
