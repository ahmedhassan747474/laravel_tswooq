<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierMainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_main', function (Blueprint $table) {
            $table->integer('supplier_main_id', true);
            $table->integer('admin_id')->nullable();
            $table->integer('user_supplier_id')->nullable();
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
        Schema::dropIfExists('supplier_main');
    }
}
