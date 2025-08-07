<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PelangganProfilTest extends TestCase
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
    public function pelanggan_dapat_melihat_halaman_profil()
    {
        // Buat user dengan role pelanggan
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);
        $this->loginAsPelanggan($pelanggan);

        // Akses halaman profil
        $response = $this->get(route('profil'));

        $response->assertStatus(200);
        $response->assertViewIs('pelanggan.profil');
        $response->assertSee($pelanggan->nama);
    }

    /** @test */
    public function pelanggan_dapat_memperbarui_data_profil()
    {
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);
        $this->loginAsPelanggan($pelanggan);

        $dataBaru = [
            'nama' => 'Nama Baru',
            'no_hp' => '081234567890',
            'alamat' => 'Alamat Baru',
        ];

        $response = $this->put(route('profil.update'), $dataBaru);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Profil berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'user_id' => $pelanggan->user_id,
            'nama' => 'Nama Baru',
            'no_hp' => '081234567890',
            'alamat' => 'Alamat Baru',
        ]);
    }
}
