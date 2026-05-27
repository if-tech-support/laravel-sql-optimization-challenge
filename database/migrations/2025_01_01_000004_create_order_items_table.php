<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            // order_id には索引あり: 注文の明細をたどる（課題02 の items 件数など）のは
            //   実運用で頻出のため、現実的なスキーマとして最初から張っている。
            $table->unsignedBigInteger('order_id')->index();
            // 一方 product_id には【あえて索引を張っていません】。
            //   課題04（集計）で「商品ごとに SUM を N+1 で回す遅さ」と
            //   「1 本の GROUP BY で一掃する速さ」の差を体感するための仕掛けです。
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price'); // 注文時点の価格（スナップショット）
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
