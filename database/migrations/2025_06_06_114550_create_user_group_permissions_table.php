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
        Schema::create('user_group_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_group_id')->index();
            $table->unsignedBigInteger('permission_id')->index();
            $table->boolean('isActive')->default(true);
            $table->timestamps();

            $table->foreign('user_group_id')
                ->references('id')
                ->on('user_groups');
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_group_permissions');
    }
};
