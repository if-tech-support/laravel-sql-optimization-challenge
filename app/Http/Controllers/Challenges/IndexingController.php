<?php

namespace App\Http\Controllers\Challenges;

use App\Http\Controllers\Controller;
use App\Models\Order;

/**
 * 【課題05】インデックスを設計する
 *
 * docs/05-indexing.md を読んでから取り組んでください。
 *
 * 特定ユーザーの「支払い済み（paid）」注文を、注文日の新しい順で取得します。
 *
 * orders テーブルにはインデックスを一切張っていません（マイグレーション参照）。
 * そのためこのクエリは 10 万件のフルスキャンになります。
 *
 * 手順:
 *   1. 下のクエリを EXPLAIN で確認し、type=ALL（フルスキャン）であることを見る
 *      （docs/how-to-measure.md にやり方があります）
 *   2. user_id / status / ordered_at に対する適切なインデックスを
 *      マイグレーションで追加する（複合インデックスの列順を考えること）
 *   3. 再度 EXPLAIN し、フルスキャンが解消されたことを確認する
 *
 * ※ クエリ自体は変えなくて構いません。改善対象は「スキーマ（インデックス）」です。
 */
class IndexingController extends Controller
{
    public function __invoke()
    {
        $userId = 1234; // 適当な対象ユーザー

        $query = Order::query()
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->orderByDesc('ordered_at');

        // EXPLAIN 結果も画面に出して、Before/After を比較しやすくしておく
        $explain = collect(\DB::select('EXPLAIN ' . $query->toSql(), $query->getBindings()));

        $orders = $query->limit(50)->get();

        return view('challenges.indexing', [
            'orders' => $orders,
            'explain' => $explain,
        ]);
    }
}
