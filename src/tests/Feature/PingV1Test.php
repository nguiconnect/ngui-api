<?php
namespace Tests\Feature;

use Tests\TestCase;

class PingV1Test extends TestCase
{
    public function test_ping_v1_returns_pong(): void
    {
        $response = $this->getJson('/api/v1/ping');
        $response->assertOk()->assertJson(['pong' => true]);
    }
}