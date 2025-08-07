<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Penumpang;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PelangganTiketTest extends TestCase
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
    public function test_pelanggan_dapat_melihat_halaman_tiket()
    {
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);
        $this->loginAsPelanggan($pelanggan);

        $response = $this->get('/tiket');

        $response->assertStatus(200);
        $response->assertViewIs('pelanggan.tiket');
    }

    public function test_pelanggan_dapat_melihat_halaman_detail_tiket()
    {
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);
        $this->loginAsPelanggan($pelanggan);
        $pemesanan = Pemesanan::factory()->create();

        $response = $this->get(route('detail.tiket', ['id' => $pemesanan->pemesanan_id]));

        $response->assertStatus(200);
        $response->assertViewIs('pelanggan.detail-tiket');
    }

    public function test_pelanggan_dapat_membatalkan_tiket()
    {
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);
        $this->loginAsPelanggan($pelanggan);

        $mobil = Mobil::factory()->create([
            'kapasitas' => 10
        ]);

        $jadwal = Jadwal::factory()->create([
            'mobil_id' => $mobil->mobil_id,
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_berangkat' => '08:00:00',
        ]);

        $pemesanan = Pemesanan::create([
            'pemesanan_id' => Str::uuid(),
            'user_id' => $pelanggan->user_id,
            'jadwal_id' => $jadwal->jadwal_id,
            'jumlah_penumpang' => 1,
            'status' => 'Menunggu Pembayaran',
            'total_harga' => 100000,
            'kode_booking' => 'BK-' . strtoupper(Str::random(6)),
        ]);

        $penumpang = Penumpang::create([
            'penumpang_id' => Str::uuid(),
            'pemesanan_id' => $pemesanan->pemesanan_id,
            'nama' => $pelanggan->nama,
            'no_hp' => $pelanggan->no_hp,
            'jenis_kelamin' => 'Laki-laki',
            'nomor_kursi' => '1',
            'alamat_jemput' => 'Jl. ABC',
            'alamat_antar' => 'Jl. XYZ',
            'jumlah_penumpang' => 1,
        ]);

        Tiket::create([
            'tiket_id' => Str::uuid(),
            'pemesanan_id' => $pemesanan->pemesanan_id,
            'no_tiket' => 'TK-' . strtoupper(Str::random(8)),
            'nama_penumpang' => $pelanggan->nama,
            'nomor_kursi' => $penumpang->nomor_kursi,
        ]);

        $response = $this->post(route('tiket.batalkan', ['id' => $pemesanan->pemesanan_id]));

        $response->assertRedirect(route('detail.tiket', ['id' => $pemesanan->pemesanan_id]));
        $response->assertSessionHas('success', 'Tiket berhasil dibatalkan dan kursi dikembalikan.');

        $this->assertDatabaseHas('pemesanans', [
            'pemesanan_id' => $pemesanan->pemesanan_id,
            'status' => 'Tiket dibatalkan',
        ]);

        $this->assertDatabaseHas('penumpangs', [
            'penumpang_id' => $penumpang->penumpang_id,
            'nomor_kursi' => null,
        ]);
    }
}
