<?php

namespace Tests\Feature;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_dengan_username_dan_password_valid_bisa_login()
    {
        $user = User::create([
            'user_id' => (string) Str::uuid(), // cast ke string
            'nama' => 'adminuser',
            'no_hp' => '081234567890',
            'alamat' => 'Jalan Testing No.1',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'username' => 'adminuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard.admin'));
        $this->assertAuthenticatedAs($user, 'admin');
    }

    /** @test */
    public function pelanggan_dengan_nomorhp_dan_password_valid_bisa_login()
    {
        $user = User::create([
            'user_id' => (string) Str::uuid(), // cast ke string
            'nama' => 'Wahyudi Rayhan',
            'no_hp' => '089620973251',
            'alamat' => 'Jalan Testing No.2',
            'password' => Hash::make('password123'),
            'role' => 'pelanggan',
        ]);

        $response = $this->post('/login-pelanggan', [
            'no_hp' => '089620973251',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('cari-jadwal'));
        $this->assertAuthenticatedAs($user, 'pelanggan');
    }

    /** @test */
    public function login_gagal_dengan_data_salah()
    {
        $response = $this->post('/login', [
            'username' => 'salah',
            'password' => 'salah123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    /** @test */
    public function user_bisa_logout()
    {
        $user = User::create([
            'user_id' => (string) Str::uuid(), // cast ke string
            'nama' => 'adminuser',
            'no_hp' => '081234567890',
            'alamat' => 'Jalan Testing No.1',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($user, 'admin');

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest('admin');
    }
}
