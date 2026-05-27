<?php

namespace App\Http\Controllers\Challenges;

use App\Http\Controllers\Controller;
use App\Models\Order;

/**
 * 【課題02】N+1 問題を解消する（Eager Loading）
 *
 * docs/02-nplus1.md を読んでから取り組んでください。
 *
 * 最新 100 件の注文について、注文者名と明細件数を一覧表示します。
 * 下の実装は注文を 100 件取得するだけですが、ビュー側で
 * $order->user->name と $order->items を参照しているため、
 * 注文ごとに追加クエリが飛びます（= N+1 問題）。
 *
 * Debugbar のクエリ数（おそらく 200 本超）を確認し、
 * with() による Eager Loading で数本に減らしてください。
 */
class NPlusOneController extends Controller
{
    public function __invoke()
    {
        // 【遅い実装】リレーションを Eager Load していない。
        // TODO: with(['user', 'items']) を付けて N+1 を解消する。
        $orders = Order::query()
            ->orderByDesc('ordered_at')
            ->limit(100)
            ->get();

        return view('challenges.nplus1', ['orders' => $orders]);
    }
}
