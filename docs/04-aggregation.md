# 課題04: 集計は PHP ではなく DB にやらせる

## 背景

「商品ごとの売上合計」のような集計を、レコードを全部 PHP に取り出して `foreach` で足し込むのは
典型的なアンチパターンです。データ転送量・メモリ・ループ時間がデータ量に比例して膨らみます。
**合計・件数・平均・並び替え・上位 N 件の抽出は、すべて DB 側でやらせる**のが鉄則です。

## 対象

- ページ: <http://localhost/challenges/04-aggregation>
- コントローラ: `app/Http/Controllers/Challenges/AggregationController.php`

現状は商品を 200 件ずつループし、商品ごとに `SUM` クエリを発行（N+1 集計）したうえで、
並び替えと上位 20 件の抽出を **PHP 側（`sortByDesc`/`take`）** で行っています。
さらに `order_items.product_id` には**あえて索引を張っていない**ため、各 `SUM` が毎回フルスキャンになり、
ページの表示に **十数秒**かかります（重さを体感してください）。

## やること

> Debugbar の見方が分からない場合は、先に [docs/how-to-measure.md](how-to-measure.md) を読んでください。

1. ブラウザで <http://localhost/challenges/04-aggregation> を開く（表示に十数秒かかる）。画面下部のバーの **`Queries` タブ**で本数（商品 200 件ぶん集計クエリが飛び **200 本超**）と、右側の **`○s`（実行時間）**を確認する。
2. `app/Http/Controllers/Challenges/AggregationController.php` を開き、売上 = `SUM(quantity * unit_price)` を `order_items` に対する **1 本の GROUP BY クエリ**で求める。
3. 並び替え（`ORDER BY 売上 DESC`）と `LIMIT 20` も **DB 側**で行う（PHP の `sortByDesc` / `take` を消す。解答例は下記）。
4. ファイルを保存し、**ブラウザを再読み込み**して `Queries` タブが **数本**に、実行時間が **十数秒 → 1 秒台**に激減することを確認する。

## 達成基準

- 集計が **1 本の GROUP BY クエリ**になっている（Debugbar 全体では数本だが、集計用クエリは 1 本）。
- 並び替え・上限も SQL 側で行われている（PHP 側の `sortByDesc`/`take` が消えている）。
- 実行時間が十数秒から 1 秒台へ大幅に短縮されている。

## ヒント

- 王道は `order_items` を起点にした **1 本の GROUP BY**：`selectRaw('product_id, SUM(quantity * unit_price) as sales')` + `groupBy('product_id')` + `orderByDesc('sales')` + `limit(20)`。これは `order_items` を **1 回だけ**走査して一気に集計します。
- 商品名も出したいなら `join('products', ...)` するか、上位 20 件の `product_id` で商品を引きます。
- ⚠️ **`withSum` の罠**: `Product::withSum('orderItems as sales', ...)` は一見きれいですが、内部的には**商品ごとに相関サブクエリ**を生成します。`order_items.product_id` に索引が無い本課題では、これは N+1 と同じく商品数ぶんのフルスキャンになり、**かえって致命的に遅くなります**（実測で数百秒）。「便利な書き方が常に速いとは限らない」「集計は 1 パスで済む GROUP BY が基本」という点をここで体感してください。索引があれば `withSum` も実用的になります（→ 課題05）。

<details>
<summary>解答例（自分で挑戦してから開く）</summary>

```php
// 王道：order_items を 1 回走査する GROUP BY（本課題ではこちらが速い）
$report = DB::table('order_items')
    ->join('products', 'products.id', '=', 'order_items.product_id')
    ->selectRaw('products.name as name, SUM(order_items.quantity * order_items.unit_price) as sales')
    ->groupBy('products.id', 'products.name')
    ->orderByDesc('sales')
    ->limit(20)
    ->get()
    ->map(fn ($r) => ['name' => $r->name, 'sales' => (int) $r->sales]);
```

```php
// 参考：withSum（簡潔だが相関サブクエリ。order_items.product_id に索引が無いと激遅）
$report = Product::query()
    ->select(['id', 'name'])
    ->withSum('orderItems as sales', DB::raw('quantity * unit_price'))
    ->orderByDesc('sales')
    ->limit(20)
    ->get()
    ->map(fn ($p) => ['name' => $p->name, 'sales' => (int) $p->sales]);
```

</details>
