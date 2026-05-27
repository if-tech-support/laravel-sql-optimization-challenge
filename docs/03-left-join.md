# 課題03: LEFT JOIN で「0 件側」を取りこぼさない

## 背景

「全商品のレビュー件数を出す」とき、`reviews` テーブルと結合します。
ここで **INNER JOIN** を使うと、レビューが 1 件もない商品は結合相手がいないため
**結果から消えてしまいます**。「0 件の商品も 0 件として表示したい」場合は **LEFT JOIN** が必要です。

これは上長が言及していた「LEFT JOIN でスピードが変わる」という話とも直結します。
N+1 で件数を数えるのではなく、1 本の JOIN クエリにまとめるのが狙いです。

## 対象

- ページ: <http://localhost/challenges/03-left-join>
- コントローラ: `app/Http/Controllers/Challenges/LeftJoinController.php`

現状は商品ごとに `reviews()->count()` を呼ぶ **N+1 集計**になっています。

## やること

> Debugbar の見方が分からない場合は、先に [docs/how-to-measure.md](how-to-measure.md) を読んでください。

1. ブラウザで <http://localhost/challenges/03-left-join> を開く。
2. 画面下部のバーの **`Queries` タブ**をクリックし、本数を確認する（商品 300 件ぶん、**300 本超**発行されているはず）。`select count(*) ... from reviews where product_id = ?` が大量に並ぶのが見える。
3. `app/Http/Controllers/Challenges/LeftJoinController.php` を開き、これを **1 本のクエリ**にまとめる。ただし**レビュー 0 件の商品が一覧から消えないように**する（解答例は下記）。
4. ファイルを保存し、**ブラウザを再読み込み**して `Queries` タブの本数が **1〜2 本**に減ったことを確認する。
5. （理解を深める任意課題）試しに `withCount` ではなく INNER JOIN（`join`）で書いてみて、**レビュー 0 件の商品が一覧から消えてしまう**ことを実際に目で見て確認する。なぜ消えるのか、LEFT JOIN との違いを説明できるようにする。

## 達成基準

- クエリ本数が **1〜2 本**になっている。
- レビュー 0 件の商品も、件数 0 として一覧に残っている。

## ヒント

- 最も簡単なのは `withCount('reviews')`。これは内部的に相関サブクエリ（LEFT JOIN 相当）を使い、0 件も残します。
- 生の JOIN で書くなら `leftJoin('reviews', ...)` + `groupBy` + `COUNT(reviews.id)`。
  `COUNT(*)` ではなく `COUNT(reviews.id)` にしないと、LEFT JOIN で NULL 行まで 1 と数えてしまう点に注意。

<details>
<summary>解答例（自分で挑戦してから開く）</summary>

```php
// withCount を使う場合（推奨・シンプル）
$products = Product::query()
    ->select(['id', 'name'])
    ->withCount('reviews')   // $product->reviews_count
    ->orderBy('id')
    ->limit(300)
    ->get();
```

```php
// LEFT JOIN を明示する場合
$products = Product::query()
    ->leftJoin('reviews', 'reviews.product_id', '=', 'products.id')
    ->groupBy('products.id', 'products.name')
    ->orderBy('products.id')
    ->limit(300)
    ->get(['products.id', 'products.name', DB::raw('COUNT(reviews.id) as reviews_count')]);
```

> ビューは配列参照（`$product['reviews_count']`）のままで動くよう、`->map()` で整形するか
> ビュー側を `$product->reviews_count` に書き換えてください。

</details>
