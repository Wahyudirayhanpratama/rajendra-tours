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

        // Tiket lama (riwayat): tanggal keberangkatan sudah lewat
        $riwayatTiket = Pemesanan::with(['jadwal', 'tiket'])
            ->where('user_id', $userId)
            ->whereIn('status', ['Lunas', 'Tiket dibatalkan'])
            ->whereHas('jadwal', function ($query) {
                $query->whereDate('tanggal', '<', Carbon::today());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelanggan.riwayat', compact('riwayatTiket'));
    }
}
