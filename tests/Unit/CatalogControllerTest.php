<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CatalogControllerTest extends TestCase
{
    /**
     * @var string $catalog
     * @dataProvider catalogs
     */
    public function testGetCatalog($catalog)
    {
        $response = $this->json('GET', url('api/v1.0/catalogs/'. $catalog));

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                ['id', 'name']
            ]]);
    }

    /**
     * @var string $catalog
     * @dataProvider catalogs
     */
    public function testGetInvalidCatalog()
    {
        $response = $this->json('GET', url('api/v1.0/catalogs/invalid'));

        $response->assertStatus(500);
    }

    /**
     * @return array
     */
    public function catalogs()
    {
        return [
           ['categories'],
        ];
    }
}
