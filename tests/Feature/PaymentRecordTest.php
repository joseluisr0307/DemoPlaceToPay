<?php

namespace Tests\Feature;

use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;


class PaymentRecordTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPayment()
    {
        /**
         * PODRÍA SER LA IDENTIFICACIÓN DE LA ORDEN DE PAGO
         */
        $reference = substr(Uuid::uuid4(), 0, 30);

        $newPayment['data'] = [
            'payment' => [
                'reference' => $reference,
                'description' => 'Testing payment',
                'amount' => [
                    'currency' => 'USD',
                    'total' => mt_rand(15, 120),
                ],
            ],
            'expiration' => date('c', strtotime(' + 2 days')),
            'returnUrl' => URL::to('/#/response?reference=' . $reference),
            'ipAddress' => '127.0.0.1',
            'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36'
        ];

        $rsp = $this->json('POST', '/api/v1/pay/subscription', $newPayment);

        $rsp->assertStatus(200);

        $rsp->assertJson([
            'success' => true,
            'error' => []
        ]);

        $responseJson = json_decode($rsp->content());

        foreach ($responseJson->data as $key => $data) {
            if ($key == 'url') {
                //Log::info('Test Registro', ['url' => $data]);
                $this->assertDatabaseHas('payments', [
                    'process_url' => $data->url
                ]);
                break;
            }
        }
    }
}
