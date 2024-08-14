<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_post', function (Blueprint $table) {
            $table->id('user_post_id');
            $table->unsignedBigInteger('user_id');
            $table->string('caption');
            $table->string('image')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('user_id')->on('user_registration')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_post');
    }
};
