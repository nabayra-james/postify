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
        Schema::table('user_post', function (Blueprint $table) {
            $table->enum('privacy', ['Public', 'Friends', 'Only Me'])->default('Public')->after('image');
        });
    }

    public function down()
    {
        Schema::table('user_post', function (Blueprint $table) {
            $table->dropColumn('privacy');
        });
    }
};
