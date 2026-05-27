<?php

namespace App\Http\Controllers\Challenges;

use App\Http\Controllers\Controller;
use App\Models\Product;

/**
 * 【課題04】集計は PHP ではなく DB にやらせる
 *
 * docs/04-aggregation.md を読んでから取り組んでください。
 *
 * 商品ごとの売上（quantity × unit_price の合計）を集計し、売上上位 20 件を表示します。
 *
 * 下の実装は商品を 1 件ずつループし、商品ごとに集計クエリを発行しています（N+1 集計）。
 * これを GROUP BY / withSum を使った 1 本のクエリで求め、
 * 並び替え（ORDER BY）と件数制限（LIMIT）も DB 側で行ってください。
 */
class AggregationController extends Controller
{
    public function __invoke()
    {
        // 【遅い実装】商品ごとに sum クエリを発行し、PHP 側で並べ替えている。
        // TODO: withSum / selectRaw + groupBy + orderByDesc + limit で 1 クエリにする。
        $report = Product::query()
            ->orderBy('id')
            ->limit(200)
            ->get()
            ->map(function (Product $product) {
                $sales = $product->orderItems()
                    ->selectRaw('SUM(quantity * unit_price) as total')
                    ->value('total') ?? 0;

                return ['name' => $product->name, 'sales' => (int) $sales];
            })
            ->sortByDesc('sales')
            ->take(20)
            ->values();

        return view('challenges.aggregation', ['report' => $report]);
    }
}
