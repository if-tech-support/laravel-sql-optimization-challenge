<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // product_id には索引あり: 商品ごとのレビュー集計（課題03）を最適化したとき
            //   ちゃんと速くなるよう、現実的なスキーマとして最初から張っている。
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('rating'); // 1〜5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
