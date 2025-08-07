<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class PelangganControllerTest extends TestCase
{
    use RefreshDatabase;

    // Setup awal: buat admin
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
    public function admin_dapat_melihat_halaman_daftar_pelanggan()
    {
        $this->loginAsAdmin();
        $response = $this->get(route('data-pelanggan'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.data-akun.data-pelanggan');
    }
    #[Test]
    public function admin_dapat_menambahkan_data_pelanggan()
    {
        $this->loginAsAdmin();
        $response = $this->post(route('store-pelanggan'), [
            'nama' => 'Budi',
            'no_hp' => '08123456789',
            'alamat' => 'Jl. Kenangan',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('data-pelanggan'));
        $this->assertDatabaseHas('users', [
            'nama' => 'Budi',
            'role' => 'pelanggan',
        ]);
    }
    #[Test]
    public function admin_dapat_mengedit_data_pelanggan()
    {
        $this->loginAsAdmin();
        $pelanggan = User::factory()->create([
            'role' => 'pelanggan',
        ]);

        $response = $this->put(route('update-data-pelanggan', $pelanggan->user_id), [
            'nama' => 'Budi Update',
            'no_hp' => '08991234567',
            'alamat' => 'Jl. Baru',
        ]);

        $response->assertRedirect(route('data-pelanggan'));
        $this->assertDatabaseHas('users', [
            'user_id' => $pelanggan->user_id,
            'nama' => 'Budi Update',
        ]);
    }
    #[Test]
    public function admin_dapat_menghapus_data_pelanggan()
    {
        $this->loginAsAdmin();
        $pelanggan = User::factory()->create([
            'role' => 'pelanggan',
        ]);

        $response = $this->delete(route('hapus-data-pelanggan', $pelanggan->user_id));
        $response->assertRedirect(route('data-pelanggan'));

        $this->assertDatabaseMissing('users', [
            'user_id' => $pelanggan->user_id,
        ]);
    }
}
