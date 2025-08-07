<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\Mobil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class JadwalControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAsAdmin()
    {
        $admin = User::create([
            'user_id' => (string) Str::uuid(),
            'nama' => 'adminuser',
            'no_hp' => '081234567890',
            'alamat' => 'Jalan Testing No.1',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'admin');
        return $admin;
    }

    /** @test */
    public function admin_dapat_melihat_daftar_jadwal()
    {
        $this->loginAsAdmin();

        $response = $this->get(route('jadwal-keberangkatan'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.jadwal-keberangkatan.jadwal-keberangkatan');
    }

    /** @test */
    public function admin_dapat_menyimpan_jadwal_baru()
    {
        $this->loginAsAdmin();

        $mobil = Mobil::create([
            'mobil_id' => (string) Str::uuid(),
            'nama_mobil' => 'Avanza',
            'nomor_polisi' => 'BM1234AA',
            'kapasitas' => 7,
            'gambar' => 'default.jpg',
        ]);

        $response = $this->post(route('store-jadwal-keberangkatan'), [
            'mobil_id' => $mobil->mobil_id,
            'kota_asal' => 'Pekanbaru',
            'kota_tujuan' => 'Padang',
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_berangkat' => '08:00',
            'harga' => 120000,
        ]);

        $response->assertRedirect('/jadwal');
        $this->assertDatabaseHas('jadwals', [
            'mobil_id' => $mobil->mobil_id,
            'kota_asal' => 'Pekanbaru',
            'kota_tujuan' => 'Padang',
        ]);
    }

    /** @test */
    public function admin_dapat_mengupdate_jadwal()
    {
        $this->loginAsAdmin();

        $mobil = Mobil::create([
            'mobil_id' => (string) Str::uuid(),
            'nama_mobil' => 'Innova',
            'nomor_polisi' => 'BM5678EF',
            'kapasitas' => 5,
        ]);

        $jadwal = Jadwal::create([
            'jadwal_id' => (string) Str::uuid(),
            'mobil_id' => $mobil->mobil_id,
            'kota_asal' => 'Pekanbaru',
            'kota_tujuan' => 'Padang',
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_berangkat' => '09:00',
            'harga' => 110000,
        ]);

        $response = $this->put(route('update-jadwal-keberangkatan', $jadwal->jadwal_id), [
            'mobil_id' => $mobil->mobil_id,
            'kota_asal' => 'Duri',
            'kota_tujuan' => 'Padang',
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_berangkat' => '10:00',
            'harga' => 120000,
        ]);

        $response->assertRedirect(route('jadwal-keberangkatan'));
        $this->assertDatabaseHas('jadwals', [
            'jadwal_id' => $jadwal->jadwal_id,
            'kota_asal' => 'Duri',
        ]);
    }

    /** @test */
    public function admin_dapat_menghapus_jadwal()
    {
        $this->loginAsAdmin();

        $mobil = Mobil::create([
            'mobil_id' => (string) Str::uuid(),
            'nama_mobil' => 'Xpander',
            'nomor_polisi' => 'BM9876GH',
            'kapasitas' => 5,
        ]);

        $jadwal = Jadwal::create([
            'jadwal_id' => (string) Str::uuid(),
            'mobil_id' => $mobil->mobil_id,
            'kota_asal' => 'Pekanbaru',
            'kota_tujuan' => 'Padang',
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'jam_berangkat' => '07:00',
            'harga' => 90000,
        ]);

        $response = $this->delete(route('hapus-jadwal-keberangkatan', $jadwal->jadwal_id));

        $response->assertRedirect(route('jadwal-keberangkatan'));
        $this->assertDatabaseMissing('jadwals', ['jadwal_id' => $jadwal->id]);
    }

    /** @test */
    public function pelanggan_bisa_mencari_jadwal()
    {
        $pelanggan = User::create([
            'user_id' => Str::uuid(),
            'nama' => 'Pelanggan Test',
            'no_hp' => '081234567891',
            'alamat' => 'Jalan Pelanggan No.2',
            'password' => Hash::make('pelanggan123'),
            'role' => 'pelanggan',
        ]);

        $mobil = Mobil::create([
            'mobil_id' => Str::uuid(),
            'nama_mobil' => 'Innova',
            'nomor_polisi' => 'BA 1234 AB',
            'kapasitas' => 6,
        ]);

        $jadwal = Jadwal::create([
            'jadwal_id' => Str::uuid(),
            'mobil_id' => $mobil->mobil_id,
            'kota_asal' => 'Pekanbaru', // ini yang benar
            'kota_tujuan' => 'Duri',    // ini juga
            'tanggal' => now()->toDateString(),
            'jam_berangkat' => '08:00:00',
            'harga' => 100000,
        ]);

        $this->actingAs($pelanggan); // pastikan login

        $response = $this->get(route('jadwal.cari') . '?' . http_build_query([
            'date' => now()->toDateString(),
            'cityfrom' => 'Pekanbaru',
            'cityto' => 'Duri',
            'jumlah_penumpang' => 1,
        ]));

        $response->assertSessionHasNoErrors();
        $response->assertStatus(200);
        $response->assertSee('Duri');
    }
}
