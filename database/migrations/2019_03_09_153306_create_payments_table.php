<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('request_id', 80)->unique()->comment('Identificador de la petición de pago');
            $table->string('process_url', 150)->unique()->comment('Url para procesamiento del pago');
            $table->tinyInteger('status_id')->unsigned()->nullable()->default(null)->comment('Estado de la petición del pago');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('status_id', 'fk_payments_status_1')->references('id')->on('status')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
