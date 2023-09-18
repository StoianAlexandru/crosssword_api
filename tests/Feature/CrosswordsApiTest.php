<?php

namespace Tests\Feature;

use App\Enum\DateFormatEnum;
use Tests\TestCase;

class CrosswordsApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testSuccess(): void
    {
        $response = $this->getJson('/api/crosswords?date='.date(DateFormatEnum::DbDate));

        $response->assertStatus(200);
    }

    public function testFail(): void
    {
        $response = $this->getJson('/api/crosswords');
        $response->assertStatus(400);
    }
}
