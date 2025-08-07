<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Penumpang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class SuratJalanControllerTest extends TestCase
{
    use RefreshDatabase;

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
    public function admin_dapat_melihat_halaman_cetak_surat_jalan()
    {
        $this->loginAsAdmin();

        // Setup mobil dan jadwal
        $mobil = Mobil::factory()->create();
        $jadwal = Jadwal::factory()->create([
            'mobil_id' => $mobil->mobil_id,
            'tanggal' => Carbon::today(),
        ]);

        // Buat pemesanan lunas dengan penumpang
        $pemesanan = Pemesanan::factory()->create([
            'jadwal_id' => $jadwal->jadwal_id,
            'status' => 'lunas',
        ]);

        $penumpang = Penumpang::factory()->create([
            'pemesanan_id' => $pemesanan->pemesanan_id,
        ]);

        $response = $this->get(route('cetak.surat-jalan', $jadwal->jadwal_id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.data-keberangkatan.cetak-surat-jalan');
        $response->assertSee($mobil->nomor_polisi);
    }
}
