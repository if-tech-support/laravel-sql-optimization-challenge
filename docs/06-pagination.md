# 課題06: 大量データのページネーション（offset の限界と cursor）

## 背景

`paginate()` は `LIMIT ... OFFSET ...` を使う **オフセットページネーション**です。
`?page=4000`（20 件刻みなら OFFSET 79980）のような深いページでは、DB は
「読み飛ばす行も一度走査する」ため、ページが深くなるほど遅くなります。
さらに合計件数を出すための `COUNT(*)` も毎回走ります。

**カーソルページネーション**（`cursorPaginate()`）は「直前の行の値より大きい/小さい」という
WHERE 条件で次ページを取るため、何ページ目でも一定の速度を保てます。
無限スクロールや「もっと見る」型の UI と相性が良い手法です。

## 対象

- ページ: <http://localhost/challenges/06-pagination>
- コントローラ: `app/Http/Controllers/Challenges/PaginationController.php`

## やること

1. `?page=1` と `?page=4000` をそれぞれ開き、Debugbar の実行時間を比較する（深いページが遅い）。
2. `paginate()` を `cursorPaginate()` に置き換える。
3. 深いページ（リンクをたどって先へ進む）でも実行時間が一定であることを確認する。

## 達成基準

- カーソルページネーションに置き換わっている。
- 深いページでの実行時間が、オフセット方式より改善していることを数字で説明できる。
- カーソル方式の **制約**（任意のページ番号へジャンプできない／安定した並び順が必須）を理解している。

## ヒント

- `cursorPaginate()` は一意で安定したソートキーが必要です。ここでは `orderBy('id')` のままで OK。
- ビューの `{{ $orders->links() }}` はカーソルでもそのまま「前へ/次へ」が出ます。
- 「ユーザーに件数やページ番号を見せたい」要件があるとカーソル方式は使えません。要件次第で使い分けることが大切です。
- 大量データを**バッチ処理**で全件なめる場合は、ページネーションではなく `chunkById()` / `lazyById()` が定石（メモリ一定）。これも調べておくと実務で役立ちます。

<details>
<summary>解答例（自分で挑戦してから開く）</summary>

```php
$orders = Order::query()
    ->select(['id', 'user_id', 'status', 'ordered_at'])
    ->orderBy('id')
    ->cursorPaginate(20);
```

```php
// バッチ処理で全件処理したいとき（参考）
Order::orderBy('id')->chunkById(1000, function ($orders) {
    foreach ($orders as $order) {
        // ...
    }
});
```

</details>
