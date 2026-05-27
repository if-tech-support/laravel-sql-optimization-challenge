<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // 【課題5】あえてインデックスを張っていません（user_id / status / ordered_at）。
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('pending'); // pending / paid / shipped / cancelled
            $table->dateTime('ordered_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
