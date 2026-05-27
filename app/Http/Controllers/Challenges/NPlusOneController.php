<?php

namespace App\Http\Controllers\Challenges;

use App\Http\Controllers\Controller;
use App\Models\Order;

/**
 * 【課題02】N+1 問題を解消する（Eager Loading）
 *
 * docs/02-nplus1.md を読んでから取り組んでください。
 *
 * 直近 300 件の注文（id の新しい順）について、注文者名と明細件数を一覧表示します。
 * 下の実装は注文を 300 件取得するだけですが、ビュー側で
 * $order->user->name と $order->items を参照しているため、
 * 注文ごとに追加クエリが飛びます（= N+1 問題）。
 *
 * Debugbar のクエリ数（おそらく 600 本超）を確認し、
 * with() による Eager Loading で数本に減らしてください。
 */
class NPlusOneController extends Controller
{
    public function __invoke()
    {
        // 【遅い実装】リレーションを Eager Load していない。
        // TODO: with(['user', 'items']) を付けて N+1 を解消する。
        $orders = Order::query()
            ->orderByDesc('id')
            ->limit(300)
            ->get();

        return view('challenges.nplus1', ['orders' => $orders]);
    }
}
