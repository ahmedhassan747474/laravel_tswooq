<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_supplier', function (Blueprint $table) {
            $table->integer('id', true);
			$table->string('name', 191);
			$table->string('country_code', 191)->nullable();
			$table->string('phone', 191)->nullable();
            $table->longText('description')->nullable();
			$table->string('status', 191)->default('1');
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
        Schema::dropIfExists('user_supplier');
    }
}
