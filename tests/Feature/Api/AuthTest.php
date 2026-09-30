<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
   use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Dylan Dev',
            'email' => 'dylan@pyrobet.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'user' 
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'token',
            'user' => ['id','name','email','role']
        ]);
    }
}
