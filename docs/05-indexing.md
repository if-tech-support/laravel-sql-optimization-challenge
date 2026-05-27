# 課題05: インデックスを設計する

## 背景

クエリをいくら綺麗に書いても、**インデックスがなければ DB はテーブル全体を走査（フルスキャン）**します。
50 万件の `orders` から特定条件の行を探すと、インデックスなしでは 50 万行を読みます。
適切なインデックスがあれば、必要な行だけをピンポイントで読めます。

このリポジトリの `orders` テーブルには、学習のため **意図的に検索用インデックスを張っていません**
（主キー `id` のみ）。`order_items.order_id` や `reviews.product_id` のように、他の課題で必要な
結合キーには現実的なスキーマとして最初から索引を張ってありますが、`orders` の検索条件用の索引は
この課題であなた自身に設計してもらいます。

## 対象

- ページ: <http://localhost/challenges/05-indexing>（EXPLAIN 結果を画面に表示しています）
- コントローラ: `app/Http/Controllers/Challenges/IndexingController.php`
- 追加先: 新しいマイグレーション（`orders` テーブルにインデックスを足す）

対象クエリ：
```sql
SELECT * FROM orders
WHERE user_id = 1234 AND status = 'paid'
ORDER BY ordered_at DESC;
```

## やること

> この課題は Debugbar ではなく、**ページ内に表示される「EXPLAIN 結果」の表**を見ます。

1. ブラウザで <http://localhost/challenges/05-indexing> を開く。ページの上部に **EXPLAIN 結果の表**が出ているので、`type` 列が `ALL`（フルスキャン）、`key` 列が空、`rows` 列が約 50 万、`Extra` に `Using filesort` が出ていることを確認する（読み方は [docs/how-to-measure.md](how-to-measure.md) の EXPLAIN の節を参照）。
2. 新しいマイグレーションファイルを作る:
   ```bash
   ./vendor/bin/sail artisan make:migration add_indexes_to_orders_table
   ```
   `database/migrations/` に生成されたファイルを開き、`orders` に適切なインデックスを追加する（**複合インデックスの列順**がポイント。解答例は下記）。
3. マイグレーションを実行する:
   ```bash
   ./vendor/bin/sail artisan migrate
   ```
4. **ブラウザを再読み込み**し、ページ上部の EXPLAIN の表で `type` が `ref` / `range` に変わり、`key` 列にインデックス名が出て、`rows` が激減（数件程度）していることを確認する。

## 達成基準

- EXPLAIN の `type` がフルスキャン（`ALL`）でなくなっている。
- `key` 列に追加したインデックス名が表示されている。
- Before/After の EXPLAIN を PR に記録している。

## ヒント

- 検索条件は `user_id`（等価）＋ `status`（等価）、並び替えは `ordered_at`。**複合インデックスの列順**がポイント。
  等価比較する列を先に、並び替え・範囲の列を後ろに置くのが定石です。
- 単一列インデックスを 3 本バラバラに張るより、1 本の複合インデックスの方が効くケースが多い理由も考えてみましょう。
- マイグレーション作成: `./vendor/bin/sail artisan make:migration add_indexes_to_orders_table`

<details>
<summary>解答例（自分で挑戦してから開く）</summary>

```php
// 新しいマイグレーション
public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        // user_id と status で絞り込み、ordered_at で並べ替えるため、この順の複合インデックス
        $table->index(['user_id', 'status', 'ordered_at'], 'orders_user_status_ordered_idx');
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropIndex('orders_user_status_ordered_idx');
    });
}
```

> 補足: `order_items.order_id` や `reviews.product_id` には最初から索引が張ってあります。
> 課題02〜04 で「最適化後はちゃんと速くなる」のはこの索引のおかげです。EXPLAIN で
> これらの結合キーが実際に使われている様子（`type=ref`）も観察してみましょう。

</details>
