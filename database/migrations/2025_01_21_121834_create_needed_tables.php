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
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('promocode')->index();
            $table->float('amount')->index();
            $table->integer('use_count')->nullable();
            $table->integer('use_max')->nullable();
            $table->timestamp('valid_till')->nullable();
            $table->timestamps();
        });
        Schema::create('promocode_user', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('promocode_id');
            $table->integer('use_count')->default(1);
            $table->unique(['user_id', 'promocode_id']);
        });
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('currency')->index()->default('RUB');
            $table->float('amount')->default(0);
            $table->text('last_action')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promocodes');
        Schema::dropIfExists('promocode_user');
        Schema::dropIfExists('balances');
    }
};
