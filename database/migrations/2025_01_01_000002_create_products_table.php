<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // 【課題5】あえてインデックス・外部キー制約を張っていません。
            //          EXPLAIN で挙動を確認し、自分で最適なインデックスを設計してください。
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
