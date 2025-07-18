<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemesanan;
use Carbon\Carbon;

class TiketController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::guard()->user()->user_id;

        $pemesanans = Pemesanan::with(['jadwal', 'penumpangs'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        // Ambil data dari session
        $cityfrom = session('cityfrom');
        $cityto = session('cityto');

        $singkatanKota = [
            'pekanbaru' => 'PKU',
            'padang' => 'PDG',
            'duri' => 'DURI',
        ];

        // Tiket aktif: tanggal keberangkatan hari ini atau yang akan datang
        $pemesanansAktif = Pemesanan::with(['jadwal', 'tiket'])
            ->where('user_id', $userId)
            ->whereHas('jadwal', function ($query) {
                $query->whereDate('tanggal', '>=', Carbon::today());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $pemesanansAktif
            ]);
        }

        return view('pelanggan.tiket', compact(
            'userId',
            'pemesanans',
            'cityfrom',
            'cityto',
            'pemesanansAktif'
        ));
        // dd(session()->all());
    }
    public function show(Request $request, $id)
    {
        $pemesanan = Pemesanan::with(['jadwal', 'tiket'])->where('pemesanan_id', $id)->firstOrFail();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $pemesanan
            ]);
        }
        return view('pelanggan.detail-tiket', compact('pemesanan'));
    }
}
