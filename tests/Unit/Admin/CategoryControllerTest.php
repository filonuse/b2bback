<?php

namespace Tests\Unit\Admin;

use App\Models\Category;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
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

        $this->user  = User::find(1); // Administrator
        $this->token = \JWTAuth::fromUser($this->user);
    }

    /**
     * Display a listing of the categories.
     */
    public function testGetList()
    {
        $response = $this->json('GET', url('api/v1.0/admin/categories'), [], ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                ['id', 'name'],
            ]]);
    }

    /**
     * Store a newly created category in storage.
     */
    public function testStore()
    {
        $response = $this->json('POST', url('api/v1.0/admin/categories'),
            ['name' => str_random(4)],
            ['Authorization' => 'Bearer ' . $this->token]
        );

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'name']]);
    }

    /**
     * Display the specified categories..
     */
    public function testShow()
    {
        $category = Category::latest()->first();
        $response = $this->json('GET', url('api/v1.0/admin/categories/'. $category->id),
            [],
            ['Authorization' => 'Bearer ' . $this->token]
        );

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name']]);
    }

    /**
     * Update the specified category in storage.
     */
    public function testUpdate()
    {
        $category = Category::latest()->first();
        $response = $this->json('PUT', url('api/v1.0/admin/categories/'. $category->id),
            ['name' => str_random(4)],
            ['Authorization' => 'Bearer ' . $this->token]
        );

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name']]);
    }

    /**
     * Remove the specified category from storage.
     */
    public function testDelete()
    {
        $category = Category::latest()->first();
        $response = $this->json('DELETE', url('api/v1.0/admin/categories/'. $category->id),
            [],
            ['Authorization' => 'Bearer ' . $this->token]
        );

        $response->assertSuccessful();
    }
}
