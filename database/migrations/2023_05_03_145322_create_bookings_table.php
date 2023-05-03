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
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id');
            $table->foreignId('room_id');
            $table->integer('pax')->default(0);
            $table->date('checkin');
            $table->date('checkout');
            $table->decimal('sub_total',20,2)->nullable();
            $table->decimal('discount',20,2)->nullable();
            $table->decimal('grand_total',20,2)->nullable();
            $table->string('notes')->nullable();
            $table->enum('status', ['pending','confirmed','canceled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
