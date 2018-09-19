<?php

namespace Tests\Unit\Client;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $token;

    protected function setUp()
    {
        parent::setUp();

        $this->user = User::query()
            ->whereHas('roles', function ($query) {
                return $query->where('name', '!=', 'admin');
            })->get()->random(1)[0];
        $this->token = \JWTAuth::fromUser($this->user);
    }

    /**
     * Display a listing of the categories.
     */
    public function testGet()
    {
        $response = $this->json('GET', url('api/v1.0/client/users'), [], ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    /**
     * Display the specified user.
     */
    public function testShow()
    {
        $user = User::latest()->first();
        $response = $this->json('GET', url('api/v1.0/client/users/' . $user->id),
            [],
            ['Authorization' => 'Bearer ' . $this->token]
        );

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name']]);
    }
}
