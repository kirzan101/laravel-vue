<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profile_user_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id')->index();
            $table->unsignedBigInteger('user_group_id')->index();
            $table->timestamps();

            $table->foreign('profile_id')
                ->references('id')
                ->on('profiles');

            $table->foreign('user_group_id')
                ->references('id')
                ->on('user_groups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_user_groups');
    }
};
