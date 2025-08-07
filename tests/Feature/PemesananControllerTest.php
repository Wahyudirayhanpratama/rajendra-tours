<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Pemesanan;
use App\Models\Penumpang;
use App\Models\Jadwal;
use App\Models\Mobil;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class PemesananControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

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

    #[Test]
    public function admin_dapat_melihat_daftar_data_pemesanan()
    {
        $this->loginAsAdmin();
        // Buat data mobil dan jadwal
        $mobil = Mobil::factory()->create();
        $jadwal = Jadwal::factory()->create(['mobil_id' => $mobil->mobil_id]);

        // Buat data pemesanan
        $pemesanan = Pemesanan::factory()->create([
            'jadwal_id' => $jadwal->jadwal_id,
        ]);

        // Buat penumpang
        Penumpang::factory()->create([
            'pemesanan_id' => $pemesanan->pemesanan_id,
        ]);

        // Login sebagai admin
        $response = $this->get(route('data-pemesanan'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.data-pemesanan.data-pemesanan');
        $response->assertViewHas('pemesanans');
        $response->assertSee($pemesanan->kode_booking);
    }

    #[Test]
    public function admin_dapat_melihat_dan_mencetak_nota_pemesanan()
    {
        $this->loginAsAdmin();
        // Setup data mobil dan jadwal
        $mobil = Mobil::factory()->create();
        $jadwal = Jadwal::factory()->create(['mobil_id' => $mobil->mobil_id]);

        // Buat data user & pemesanan
        $user = User::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $user->user_id,
            'jadwal_id' => $jadwal->jadwal_id,
        ]);

        // Buat penumpang
        Penumpang::factory()->create([
            'pemesanan_id' => $pemesanan->pemesanan_id,
        ]);

        // Akses halaman cetak nota
        $response = $this->get(route('cetak.nota', $pemesanan->pemesanan_id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.data-pemesanan.cetak-nota');
        $response->assertViewHas('pemesanan');
        $response->assertSee($pemesanan->kode_booking);
    }
}
