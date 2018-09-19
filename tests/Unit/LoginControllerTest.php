<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    public function testLoginAndLogout()
    {
        // Invalid Credentials
        $response = $this->json('POST', url('api/v1.0/login'), ['phone' => 'number_phone', 'password' => 'secret']);

        $response->assertStatus(400)
            ->assertJsonStructure(['message', 'errors']);

        // Create a new user
        $user = factory(User::class)->create([
            'name'       => 'Test User',
            'legal_name' => 'Test User',
        ]);

        // Handle a login request to the application
        $response = $this->json('POST', url('api/v1.0/login'), ['phone' => $user->phone, 'password' => 'secret']);
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'token']);

        // Log the user out of the application.
        $content  = $response->decodeResponseJson();
        $response = $this->withHeader('Authorization', 'Bearer ' . $content['token'])->json('GET', url('api/v1.0/logout'));
        $response->assertStatus(200);

        $user->delete();
    }
}