<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->integer('supplier_id', true);
            $table->integer('inventory_id')->default(0);
            $table->integer('admin_id')->nullable();
            $table->integer('user_supplier_id')->nullable();
            $table->integer('stock')->nullable();
			$table->decimal('price', 10)->nullable();
			$table->dateTime('date_added')->nullable();
			$table->dateTime('last_modified')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}
