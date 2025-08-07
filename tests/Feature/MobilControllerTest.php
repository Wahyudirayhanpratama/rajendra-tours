<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Mobil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MobilControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAsAdmin()
    {
        $admin = User::create([
            'user_id' => (string) Str::uuid(),
            'nama' => 'Admin Test',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Admin',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'admin'); // Sesuaikan jika tidak pakai guard 'admin'
        return $admin;
    }

    /** @test */
    public function admin_bisa_melihat_halaman_data_mobil()
    {
        $this->loginAsAdmin();

        $response = $this->get(route('data-mobil'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.data-mobil.data-mobil');
    }

    /** @test */
    public function admin_bisa_menambahkan_data_mobil_baru()
    {
        $this->loginAsAdmin();
        Storage::fake('public');

        $response = $this->post(route('store-data-mobil'), [
            'nama_mobil' => 'Avanza',
            'nomor_polisi' => 'BM1234XYZ',
            'kapasitas' => 6,
            'gambar' => UploadedFile::fake()->create('mobil.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect(route('data-mobil'));
        $this->assertDatabaseHas('mobils', [
            'nama_mobil' => 'Avanza',
            'nomor_polisi' => 'BM1234XYZ',
        ]);
    }

    /** @test */
    public function admin_bisa_mengedit_data_mobil()
    {
        $this->loginAsAdmin();

        $mobil = Mobil::create([
            'mobil_id' => Str::uuid(),
            'nama_mobil' => 'Toyota Lama',
            'nomor_polisi' => 'BM0001ABC',
            'kapasitas' => 4,
        ]);

        $response = $this->put(route('update-data-mobil', $mobil->mobil_id), [
            'nama_mobil' => 'Toyota Baru',
            'nomor_polisi' => 'BM0001ABC',
            'kapasitas' => 5,
        ]);

        $response->assertRedirect(route('data-mobil'));
        $this->assertDatabaseHas('mobils', [
            'mobil_id' => $mobil->mobil_id,
            'nama_mobil' => 'Toyota Baru',
            'kapasitas' => 5,
        ]);
    }

    /** @test */
    public function admin_bisa_menghapus_data_mobil()
    {
        $this->loginAsAdmin();

        $mobil = Mobil::create([
            'mobil_id' => Str::uuid(),
            'nama_mobil' => 'Mobil Delete',
            'nomor_polisi' => 'BMDEL123',
            'kapasitas' => 6,
        ]);

        $response = $this->delete(route('hapus-data-mobil', $mobil->mobil_id));

        $response->assertRedirect(route('data-mobil'));
        $this->assertDatabaseMissing('mobils', [
            'mobil_id' => $mobil->mobil_id,
        ]);
    }
}
