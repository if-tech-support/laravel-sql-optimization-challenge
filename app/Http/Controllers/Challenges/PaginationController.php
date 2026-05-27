<?php

namespace App\Http\Controllers\Challenges;

use App\Http\Controllers\Controller;
use App\Models\Order;

/**
 * 【課題06】大量データのページネーション（offset の限界と cursor）
 *
 * docs/06-pagination.md を読んでから取り組んでください。
 *
 * 注文一覧をページネーションします。下の実装は通常の paginate()（OFFSET 方式）です。
 *
 * ?page=1 は速いですが、?page=4000 のような深いページにアクセスすると
 * 「OFFSET 79980」のように大量の行を読み飛ばすため遅くなります。
 * Debugbar で page=1 と深いページの実行時間を比較してください。
 *
 * その後、cursorPaginate() に置き換えると深いページでも一定速度になることを確認してください。
 * （cursor 方式の制約 = ページ番号ジャンプができない、も docs で理解すること）
 */
class PaginationController extends Controller
{
    public function __invoke()
    {
        // 【遅い実装】OFFSET 方式のページネーション。
        // TODO: cursorPaginate(20) に置き換え、深いページでの挙動を比較する。
        $orders = Order::query()
            ->orderBy('id')
            ->paginate(20);

        return view('challenges.pagination', ['orders' => $orders]);
    }
}
