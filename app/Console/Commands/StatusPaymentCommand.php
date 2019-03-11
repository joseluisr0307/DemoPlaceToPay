<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Http\Controllers\ConnectController;

class StatusPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'validates status update for payment requests that are pending';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rejectedState = 1;
        $processState = 2;
        $approvedState = 3;

        try {

            Log::info('Command status started');

            // Inicializar transaccion
            DB::beginTransaction();

            $payments = Payment::where('status_id', $processState)
                ->orWhereNull('status_id')
                ->get();

            if ($payments->count() > 0) {

                $conection = ConnectController::singleton();
                $placetopay = $conection->getConnection();

                foreach ($payments as $payment) {

                    $result = $placetopay->query($payment->request_id);
                    if ($result->isSuccessful()) {
                        //Log::info('Payment Info',['Successful' => $result]);
                        if ($result->status()->isApproved()) {
                            $payment->status_id = $approvedState;
                            $payment->save();
                            Log::info('Payment Info', ['Approved' => $result]);
                        } elseif ($result->status()->isRejected()) {
                            $payment->status_id = $rejectedState;
                            $payment->save();
                        }
                    }

                }
            }

            // Confirmar transaccion
            DB::commit();

        } catch (Exception $e) {
            Log::error('Command status', ['message' => $e->getMessage()]);
            // Reversar transaccion
            DB::rollback();

        }

        Log::info('Command status finished');
    }
}
