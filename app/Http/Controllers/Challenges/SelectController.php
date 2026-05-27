<?php

namespace App\Http\Controllers\Challenges;

use App\Http\Controllers\Controller;
use App\Models\Product;

/**
 * 【課題01】必要なカラムだけ取得する（SELECT * を避ける）
 *
 * docs/01-select.md を読んでから取り組んでください。
 *
 * このページでは商品の「名前」と「価格」だけを一覧表示します。
 * それなのに下の実装は全カラム（description などを含む）・全件を取得しています。
 * Debugbar の「実行時間」とメモリ使用量を確認し、必要なカラムだけ取得するよう最適化してください。
 */
class SelectController extends Controller
{
    public function __invoke()
    {
        // 【遅い実装】SELECT * で全カラムを取得している。
        // TODO: 表示に使う id / name / price だけを select() で取得する。
        $products = Product::query()
            ->orderBy('id')
            ->limit(2000)
            ->get();

        return view('challenges.select', ['products' => $products]);
    }
}
