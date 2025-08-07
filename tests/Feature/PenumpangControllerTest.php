<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Penumpang;
use App\Models\Tiket;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PenumpangControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $pelanggan;
    protected $jadwal;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user
        $this->pelanggan = User::factory()->create(['role' => 'pelanggan']);

        // Buat mobil dan jadwal
        $mobil = Mobil::factory()->create(['kapasitas' => 5]);
        $this->jadwal = Jadwal::factory()->create([
            'mobil_id' => $mobil->mobil_id,
            'tanggal' => Carbon::tomorrow(),
            'jam_berangkat' => '08:00:00',
            'harga' => 100000
        ]);
    }

    protected function loginAsAdmin()
    {
        $admin = User::create([
            'user_id' => (string) Str::uuid(),
            'nama' => 'adminuser',
            'no_hp' => '081234567890',
            'alamat' => 'Alamat Admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'admin');
        return $admin;
    }
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
    public function admin_dapat_melihat_data_penumpang()
    {
        $this->loginAsAdmin();

        $response = $this->get(route('data.penumpang'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.data-penumpang.penumpang');
    }

    /** @test */
    public function admin_dapat_menambahkan_penumpang_baru()
    {
        $this->loginAsAdmin();

        $response = $this->post(route('store-data-penumpang'), [
            'user_id' => $this->pelanggan->user_id,
            'jadwal_id' => $this->jadwal->jadwal_id,
            'jumlah_penumpang' => 2,
            'nama' => 'Pelanggan 1',
            'no_hp' => '08123456789',
            'jenis_kelamin' => 'L',
            'nomor_kursi' => '1,2',
            'alamat_jemput' => 'Jl. Jemput',
            'alamat_antar' => 'Jl. Antar',
            'metode_pembayaran' => 'cod'
        ]);

        $response->assertRedirect(route('data.penumpang'));
        $this->assertDatabaseCount('penumpangs', 1);
        $this->assertDatabaseCount('pemesanans', 1);
        $this->assertDatabaseCount('tikets', 1);
    }

    /** @test */
    public function admin_dapat_mengupdate_data_penumpang()
    {
        $this->loginAsAdmin();

        // Buat pemesanan dan penumpang
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->pelanggan->user_id,
            'jadwal_id' => $this->jadwal->jadwal_id,
            'jumlah_penumpang' => 2,
            'total_harga' => 200000,
            'status' => 'lunas',
        ]);

        $penumpang = Penumpang::factory()->create([
            'pemesanan_id' => $pemesanan->pemesanan_id,
            'jenis_kelamin' => 'L',
            'nomor_kursi' => '1,2',
        ]);

        $response = $this->put(route('update-data-penumpang', $penumpang->penumpang_id), [
            'jumlah_penumpang' => 2,
            'jenis_kelamin' => 'P',
            'nomor_kursi' => '3,4',
            'alamat_jemput' => 'Jl. Update Jemput',
            'alamat_antar' => 'Jl. Update Antar',
        ]);

        $response->assertRedirect(route('data.penumpang'));
        $this->assertDatabaseHas('penumpangs', [
            'penumpang_id' => $penumpang->penumpang_id,
            'jenis_kelamin' => 'P',
            'nomor_kursi' => '3,4',
        ]);
    }

    /** @test */
    public function admin_dapat_menghapus_data_penumpang_dan_pemesanan()
    {
        $this->loginAsAdmin();

        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->pelanggan->user_id,
            'jadwal_id' => $this->jadwal->jadwal_id,
        ]);

        $penumpang = Penumpang::factory()->create([
            'pemesanan_id' => $pemesanan->pemesanan_id,
        ]);

        $response = $this->delete(route('hapus-data-penumpang', $penumpang->penumpang_id));

        $response->assertRedirect(route('data.penumpang'));
        $this->assertDatabaseMissing('penumpangs', ['penumpang_id' => $penumpang->penumpang_id]);
        $this->assertDatabaseMissing('pemesanans', ['pemesanan_id' => $pemesanan->pemesanan_id]);
    }
    /** @test */
    public function pelanggan_dapat_menyimpan_data_penumpang_dan_pemesanan_tiket()
    {
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);
        $this->loginAsPelanggan($pelanggan);

        // Buat mobil dan jadwal
        $mobil = Mobil::factory()->create(['kapasitas' => 5]);
        $jadwal = Jadwal::factory()->create([
            'mobil_id' => $mobil->mobil_id,
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'jam_berangkat' => '08:00:00',
            'harga' => 150000,
        ]);

        // Simulasikan request
        $response = $this->post(route('penumpang.store'), [
            'jadwal_id' => $jadwal->jadwal_id,
            'total_harga' => 150000,
            'jumlah_penumpang' => 1,
            'nama' => 'Test Pelanggan',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
            'nomor_kursi' => ['1'],
            'alamat_jemput' => 'Jl. Pelanggan Jemput',
            'alamat_antar' => 'Jl. Pelanggan Antar',
        ]);

        // Arahkan ke route pembayaran
        $response->assertRedirectContains('/bayar');

        // Pastikan data tersimpan di database
        $this->assertDatabaseHas('pemesanans', [
            'user_id' => $pelanggan->user_id,
            'jumlah_penumpang' => 1,
            'total_harga' => 150000,
            'status' => 'belum_lunas',
        ]);

        $this->assertDatabaseHas('penumpangs', [
            'nama' => 'Test Pelanggan',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
            'alamat_jemput' => 'Jl. Pelanggan Jemput',
            'alamat_antar' => 'Jl. Pelanggan Antar',
        ]);
        // Cek data tiket
        $this->assertDatabaseHas('tikets', [
            'nama_penumpang' => 'Test Pelanggan',
            'nomor_kursi' => '1', // hasil dari implode
        ]);
    }
}
