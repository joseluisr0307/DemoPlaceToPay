<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetPaymentsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRecords()
    {
        $rsp = $this->json('GET', '/api/v1/pay/records');

        $rsp->assertStatus(200);

        $rsp->assertJson([
            'success' => true,
            'error' => []
        ]);
    }
}
