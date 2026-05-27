<?php

namespace App\Http\Controllers\Challenges;

use App\Http\Controllers\Controller;
use App\Models\Product;

/**
 * 【課題03】LEFT JOIN で「0 件側」を取りこぼさない
 *
 * docs/03-left-join.md を読んでから取り組んでください。
 *
 * 全商品（レビューが 1 件もない商品も含む）について、レビュー件数を一覧表示します。
 *
 * 下の実装は商品ごとに reviews()->count() を呼ぶ N+1 集計です。
 * これを 1 本のクエリにまとめたいのですが、ここで JOIN の種類が重要になります。
 *   - INNER JOIN にすると「レビュー 0 件の商品」が結果から消えてしまう
 *   - LEFT JOIN（= withCount）なら 0 件の商品も件数 0 で残る
 *
 * leftJoin もしくは withCount('reviews') を使い、0 件商品を残したまま 1 クエリにしてください。
 */
class LeftJoinController extends Controller
{
    public function __invoke()
    {
        // 【遅い実装】商品ごとにレビュー件数を数えている（N+1 集計）。
        // TODO: withCount('reviews') か leftJoin + groupBy で 1 クエリにする。
        //       このとき INNER JOIN にしてレビュー 0 件の商品が消えないよう注意。
        $products = Product::query()
            ->orderBy('id')
            ->limit(300)
            ->get()
            ->map(function (Product $product) {
                return [
                    'name' => $product->name,
                    'reviews_count' => $product->reviews()->count(),
                ];
            });

        return view('challenges.left-join', ['products' => $products]);
    }
}
