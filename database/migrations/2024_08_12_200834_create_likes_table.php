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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id');
            $table->timestamps();
    
            $table->foreign('user_id')->references('user_id')->on('user_registration')->onDelete('cascade');
            $table->foreign('post_id')->references('user_post_id')->on('user_post')->onDelete('cascade');
            $table->unique(['user_id', 'post_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('likes');
    }
};
