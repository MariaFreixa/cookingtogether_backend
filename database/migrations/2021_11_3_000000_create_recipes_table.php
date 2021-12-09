<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('main_image');
            $table->integer('diners');
            $table->string('video');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users');
            $table->unsignedBigInteger('id_cateogry');
            $table->foreign('id_cateogry')->references('id')->on('categories');
            $table->unsignedBigInteger('id_complexity');
            $table->foreign('id_complexity')->references('id')->on('complexity');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE recipes MODIFY main_image MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipes');
    }
}
