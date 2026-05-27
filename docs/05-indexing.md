# 課題05: インデックスを設計する

## 背景

クエリをいくら綺麗に書いても、**インデックスがなければ DB はテーブル全体を走査（フルスキャン）**します。
10 万件の `orders` から特定条件の行を探すと、インデックスなしでは 10 万行を読みます。
適切なインデックスがあれば、必要な行だけをピンポイントで読めます。

このリポジトリのマイグレーションは、学習のため **意図的にインデックスを一切張っていません**。

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

1. ページを開き、EXPLAIN の `type` が `ALL`（フルスキャン）であることを確認する。
   （`docs/how-to-measure.md` の EXPLAIN の読み方を参照）
2. 新しいマイグレーションを作り、`orders` に適切なインデックスを追加する。
3. `migrate` 後にページを再読み込みし、`type` が `ref`/`range` に変わり `rows` が激減することを確認する。

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

> 余裕があれば、課題02〜04 で使った `order_items.order_id` / `reviews.product_id` などにも
> インデックスが必要かを EXPLAIN で検証してみましょう（JOIN 先の結合キーは効きやすい）。

</details>
