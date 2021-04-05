<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_description', function (Blueprint $table) {
            $table->integer('brand_description_id', true);
			$table->integer('brand_id')->default(0);
			$table->integer('language_id')->default(1);
			$table->string('brand_name', 32)->index('idx_brand_name');
			$table->text('brand_description', 65535)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_description');
    }
}
