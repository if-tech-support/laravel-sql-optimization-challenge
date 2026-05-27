# 課題04: 集計は PHP ではなく DB にやらせる

## 背景

「商品ごとの売上合計」のような集計を、レコードを全部 PHP に取り出して `foreach` で足し込むのは
典型的なアンチパターンです。データ転送量・メモリ・ループ時間がデータ量に比例して膨らみます。
**合計・件数・平均・並び替え・上位 N 件の抽出は、すべて DB 側でやらせる**のが鉄則です。

## 対象

- ページ: <http://localhost/challenges/04-aggregation>
- コントローラ: `app/Http/Controllers/Challenges/AggregationController.php`

現状は商品を 1 件ずつループし、商品ごとに `SUM` クエリを発行（N+1 集計）したうえで、
並び替えと上位 20 件の抽出を **PHP 側（`sortByDesc`/`take`）** で行っています。

## やること

1. 売上 = `SUM(quantity * unit_price)` を、`order_items` に対する **1 本の GROUP BY クエリ**で求める。
2. 並び替え（`ORDER BY 売上 DESC`）と `LIMIT 20` も **DB 側**で行う。
3. Debugbar でクエリ本数が 1 本に、実行時間が大幅に減ることを確認する。

## 達成基準

- クエリ本数が **1 本**。
- 並び替え・上限も SQL 側で行われている（PHP 側の `sortByDesc`/`take` が消えている）。

## ヒント

- `OrderItem` を起点に `selectRaw('product_id, SUM(quantity * unit_price) as sales')` + `groupBy('product_id')` + `orderByDesc('sales')` + `limit(20)`。
- 商品名も出したいなら `join('products', ...)` するか、上記で得た上位 20 件の `product_id` で商品を引く。
- Eloquent 的に書くなら `Product::withSum('orderItems as sales', DB::raw('quantity * unit_price'))->orderByDesc('sales')->limit(20)` も使えます（`withSum` は LEFT JOIN 相当なので売上 0 の商品も残ります）。

<details>
<summary>解答例（自分で挑戦してから開く）</summary>

```php
// withSum を使う場合（商品名も自然に取れる）
$report = Product::query()
    ->select(['id', 'name'])
    ->withSum('orderItems as sales', DB::raw('quantity * unit_price'))
    ->orderByDesc('sales')
    ->limit(20)
    ->get()
    ->map(fn ($p) => ['name' => $p->name, 'sales' => (int) $p->sales]);
```

```php
// 生の GROUP BY で書く場合
$report = DB::table('order_items')
    ->join('products', 'products.id', '=', 'order_items.product_id')
    ->selectRaw('products.name as name, SUM(order_items.quantity * order_items.unit_price) as sales')
    ->groupBy('products.id', 'products.name')
    ->orderByDesc('sales')
    ->limit(20)
    ->get()
    ->map(fn ($r) => ['name' => $r->name, 'sales' => (int) $r->sales]);
```

</details>
