<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourierShipmentInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courier_shipment_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('courier_name'); // e.g., 'Pathao'
            $table->string('consignment_id')->nullable();
            $table->string('merchant_order_id')->nullable();
            $table->decimal('delivery_fee', 8, 2)->nullable();
            $table->string('order_status')->nullable();
            $table->json('response_data')->nullable(); // Store full response
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courier_shipment_infos');
    }
}
