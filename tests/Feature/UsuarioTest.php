<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_se_puede_crear(): void
    {
        $response = $this->postJson('/api/usuarios', [
            'name' => 'Carlos',
            'email' => 'carlos@test.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(201)->assertJsonFragment(['name' => 'Carlos']);
    }
}
