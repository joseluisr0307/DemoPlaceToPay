<?php

namespace Tests\Feature;

use Tests\TestCase;

class StatusPaymentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetStatus()
    {
        $data = [
            'reference' => 121212
        ];

        $rsp = $this->json('POST', '/api/v1/pay/query/status', $data);

        $rsp->assertStatus(200);

        $rsp->assertJson([
            'success' => true,
            'error' => []
        ]);

        $rsp->assertJsonStructure([
            'data',
            'success',
            'error'
        ]);

    }
}
