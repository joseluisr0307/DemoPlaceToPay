<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class PaymentController extends Controller
{

    /**
     * Cuando el usuario confirme la compra, el método createRequest debe
     * ser consumido enviando la información sobre el cobro a realizar, datos que se tengan
     * sobre el cliente, expiración (Fecha hasta que puede ser realizado el pago), URL de
     * retorno y demás valores definidos posteriormente
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     * @access public
     */
    public function basicPayment(Request $request)
    {
        $response = [
            'data' => [],
            'success' => false,
            'error' => []
        ];

        try {

            $placetopay = $this->connect();

            $paymentData = $request->data;

            /**
             *  Se adiciona infomación que no es generada por el usuario y es necesaria para el registro del pago
             */
            $reference = substr(Uuid::uuid4(), 0, 30);
            $paymentData['payment']['reference'] = $reference;
            $paymentData['expiration'] = date('c', strtotime(' + 2 days'));
            $paymentData['returnUrl'] = URL::to('/reference=' . $reference);

            //Log::info('Registro', ['Petición' => $paymentData]);
            /**
             * Si la solicitud es correcta, el servicio retorna un identificador para la petición (requestId) y
             * una URL de procesamiento (processUrl), la cual se debe enviar al cliente para continuar
             * el proceso. En caso contrario, se indica el motivo de rechazo de la petición.
             */
            $result = $placetopay->request($paymentData);

            // Inicializar transaccion
            DB::beginTransaction();

            if ($result->isSuccessful()) {

                //Log::info('Registro', ['URL' => $result->processUrl()]);
                //Log::info('Registro', ['RequestId' => $result->processUrl()]);

                $response['data'][]['url'] = $result->processUrl();

                $payment = new Payment();
                $payment->request_id = $result->requestId();
                $payment->process_url = $result->processUrl();
                $payment->reference = $reference;
                $payment->description = $paymentData['payment']['description'];
                $payment->total = $paymentData['payment']['amount']['total'];
                $payment->currency = $paymentData['payment']['amount']['currency'];
                $payment->save();

            } else {
                // There was some error so check the message and log it
                $response['data'][]['msg'] = $result->status()->message();
            }

            //Log::info('Registro', ['Respuesta' => $result]);
            $response['success'] = true;
            // Confirmar transaccion
            DB::commit();

        } catch (Exception $e) {
            // Reversar transaccion
            DB::rollback();
            Log::error('PaymentController@basicPayment', ['message' => $e->getMessage()]);
            $response['error'][]['msg'] = $e->getMessage();
        }
        return \Response::json($response, 200);
    }

    /**
     * Cuando el usuario realice el pago en la plataforma de playToPlay se consulta el estado de este último
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     * @access public
     */
    public function getStatusPayment(Request $request)
    {
        $response = [
            'data' => [],
            'success' => false,
            'error' => []
        ];

        $rejectedState = 1;
        $processState = 2;
        $approvedState = 3;

        try {

            $placetopay = $this->connect();
            $payment = Payment::where('reference', $request->reference)
                ->first();

            $result = $placetopay->query($payment->request_id);

            if ($result->isSuccessful()) {
                // Inicializar transaccion
                DB::beginTransaction();

                //Log::info('Payment Info',['Successful' => $result]);
                if ($result->status()->isApproved()) {
                    $payment->status_id = $approvedState;
                    $payment->save();
                    $response['data'][]['msg'] = 'Pago Aceptado';
                    Log::info('Payment Info', ['Approved' => $result]);
                } elseif ($result->status()->isRejected()) {
                    $payment->status_id = $rejectedState;
                    $payment->save();
                    $response['data'][]['msg'] = 'Pago Rechazado';
                } else {
                    $response['data'][]['msg'] = 'Estado Desconocido';
                }
            } else {
                $response['data'][]['msg'] = $result->status()->message();
            }
            $response['success'] = true;
            // Confirmar transaccion
            DB::commit();
        } catch (Exception $e) {
            // Reversar transaccion
            DB::rollback();
            Log::error('PaymentController@basicPayment', ['message' => $e->getMessage()]);
            $response['error'][]['msg'] = $e->getMessage();
        }
        return \Response::json($response, 200);
    }


    /**
     * Cuando el usuario necesite conocer el estado de sus pagos realizados
     *
     * @return json
     * @access public
     */
    public function payments()
    {
        $response = [
            'data' => [],
            'success' => false,
            'error' => []
        ];

        try {
            $payments = Payment::with('state')->get();

            foreach ($payments as $payment) {
                $status = $payment->state;
                $response['data'][] = [
                    'createdAt' => $payment->created_at->format('Y-m-d'),
                    'status' => ($status !== null) ? ucfirst($status->name) : 'Desconocido',
                    'reference' => ucfirst($payment->reference),
                    'description' => ucfirst($payment->description),
                    'total' => $payment->total,
                    'currency' => strtoupper($payment->currency)
                ];
            }

        } catch (Exception $e) {
            Log::error('PaymentController@basicPayment', ['message' => $e->getMessage()]);
            $response['error'][]['msg'] = $e->getMessage();
        }
        return \Response::json($response, 200);
    }

    /**
     * Conectarse a PlaceToplay
     *
     * @return Dnetix\Redirection\PlacetoPay
     * @access private
     */
    private function connect()
    {
        $conection = new ConnectController();
        return $conection->getConnection();
    }

}