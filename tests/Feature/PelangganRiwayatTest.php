<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Pemesanan;
use App\Models\Jadwal;
use App\Models\Tiket;
use App\Models\Mobil;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PelangganRiwayatTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAsPelanggan($user = null)
    {
        if (!$user) {
            $user = User::create([
                'user_id' => (string) Str::uuid(),
                'nama' => 'pelangganuser',
                'no_hp' => '081234567891',
                'alamat' => 'Alamat Pelanggan',
                'password' => Hash::make('password123'),
                'role' => 'pelanggan',
            ]);
        }

        $this->actingAs($user, 'pelanggan');
        return $user;
    }

    /** @test */
    public function pelanggan_dapat_melihat_halaman_riwayat()
    {
        // 1. Buat user pelanggan
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);
        $this->loginAsPelanggan($pelanggan);

        // 2. Buat mobil dan jadwal yang sudah lewat
        $mobil = Mobil::factory()->create();

        $jadwal = Jadwal::factory()->create([
            'mobil_id' => $mobil->mobil_id,
            'tanggal' => now()->subDay()->format('Y-m-d'),
            'jam_berangkat' => now()->subHours(2)->format('H:i:s'),
        ]);

        // 3. Buat pemesanan status Lunas
        $pemesanan = Pemesanan::create([
            'pemesanan_id' => Str::uuid(),
            'user_id' => $pelanggan->user_id,
            'jadwal_id' => $jadwal->jadwal_id,
            'status' => 'Lunas',
            'jumlah_penumpang' => 1,
            'total_harga' => 100000,
            'kode_booking' => 'BK-' . strtoupper(Str::random(6)),
        ]);

        // 4. Tambah tiket untuk pemesanan
        Tiket::create([
            'tiket_id' => Str::uuid(),
            'pemesanan_id' => $pemesanan->pemesanan_id,
            'no_tiket' => 'TK-' . strtoupper(Str::random(8)),
            'nama_penumpang' => 'Test Penumpang',
            'nomor_kursi' => '1',
        ]);

        // 5. Akses halaman riwayat
        $response = $this->get(route('riwayat'));

        // 6. Cek hasil
        $response->assertStatus(200);
        $response->assertViewIs('pelanggan.riwayat');
        $response->assertSee($pemesanan->kode_booking);
    }
}
