<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('set null');
            $table->double('product_price',10,2)->nullable();
            $table->double('total_price',10,2)->nullable();
            $table->double('shipping_price',10,2)->nullable();
            $table->tinyInteger('notFoundProduct')->nullable();
            $table->double('discount_price',10,2)->nullable();
            $table->tinyInteger('payment_method')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->unsignedBigInteger('discount_code_id')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->foreign('address_id')->references('id')->on('user_addresses')->onDelete('set null');
            $table->text('note')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
