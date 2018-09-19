<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterControllerTest extends TestCase
{
    /**
     * @dataProvider roles
     */
    public function testRegister($role)
    {
        // Make a new user
        $user = factory(User::class)->make([
            'role'                  => $role,
            'password'              => 'secret',
            'password_confirmation' => 'secret',
        ]);

        if ($role === 'provider') {
            $user->categories = Category::all()
                ->random(2)
                ->pluck('id')
                ->toArray();
        }

        // Invalid Credentials
        $response = $this->json('POST', url('api/v1.0/registration'), $user->toArray());
        $response->assertStatus(422)
            ->assertJsonStructure(['errors']);

        // Create a new user
        $user->makeVisible(['password']);

        $response = $this->json('POST', url('api/v1.0/registration'), $user->toArray());
        $response->assertStatus(201)
            ->assertJsonStructure(['data', 'token']);
    }

    /**
     * @return array
     */
    public function roles()
    {
        return [
            ['provider'],
            ['customer'],
        ];
    }
}
