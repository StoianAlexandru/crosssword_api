<?php

namespace Tests\Feature;

use App\Enum\DateFormatEnum;
use Tests\TestCase;

class CrosswordsApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testSuccessEmpty(): void
    {
        $response = $this->getJson('/api/crosswords?date=' . date(DateFormatEnum::DbDate));

        if (empty($response->json())) {
            $response->assertSuccessful();
        } else {
            $response->assertNotFound();
        }
    }

    public function testSuccessNotEmpty(): void
    {
        $response = $this->getJson('/api/crosswords?date=2023-07-25');

        if (!empty($response->json())) {
            $response->assertSuccessful();
        } else {
            $response->assertNotFound();
        }
    }

    public function testFail(): void
    {
        $response = $this->getJson('/api/crosswords');
        $response->assertStatus(400);
    }
}
