<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_detail', function (Blueprint $table) {
            $table->integer('supplier_detail_id', true);
            $table->integer('supplier_id')->default(0);
            $table->integer('admin_id')->nullable();
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
        Schema::dropIfExists('supplier_detail');
    }
}
