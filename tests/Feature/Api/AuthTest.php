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
        
        $this->artisan('passport:keys');

        $this->artisan('passport:client', [
            '--personal' => true,
            '--name' => 'Personal Access Client',
            '--provider' => 'users',
            '--no-interaction' => true,
        ]);
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Dylan Dev',
            'email' => 'dylan@pyrobet.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123', 
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'token',
            'user' => ['id','name','email','role']
        ]);
    }
}
