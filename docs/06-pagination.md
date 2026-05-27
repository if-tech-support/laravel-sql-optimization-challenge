# 課題06: 大量データのページネーション（offset の限界と cursor）

## 背景

`paginate()` は `LIMIT ... OFFSET ...` を使う **オフセットページネーション**です。これには 2 つのコストがあります。

1. **深いページほど遅い**: `?page=24000`（20 件刻みなら OFFSET 479980）では、DB は読み飛ばす 48 万行も一度たどる必要があり、ページが深くなるほど遅くなります。
2. **毎ページ `COUNT(*)`**: 合計件数とページ数を出すため、`paginate()` は毎回 `SELECT COUNT(*)` を全件に走らせます。これは 1 ページ目でもかかる固定コストです。

**カーソルページネーション**（`cursorPaginate()`）は「直前の行の値より大きい/小さい」という
WHERE 条件で次ページを取るため、`COUNT(*)` も読み飛ばしも不要で、**何ページ目でも一定の速度**を保てます。
無限スクロールや「もっと見る」型の UI と相性が良い手法です。

> ⚠️ この課題は他より wall-clock の差が穏やかです。本リポジトリは `id`（主キー）順で並べており、
> 主キー索引のおかげで OFFSET の読み飛ばしが比較的安く済むためです。**本番規模（数百万〜数千万件）**に
> なると OFFSET の読み飛ばしと毎回の `COUNT(*)` が急激に重くなり、カーソル方式との差が桁違いに開きます。
> ここでは「offset は深さに比例 + COUNT 固定費」「cursor はどちらも不要で一定」という**性質の違い**を理解することが目的です。

## 対象

- ページ: <http://localhost/challenges/06-pagination>
- コントローラ: `app/Http/Controllers/Challenges/PaginationController.php`

## やること

> Debugbar の見方が分からない場合は、先に [docs/how-to-measure.md](how-to-measure.md) を読んでください。

1. ブラウザで <http://localhost/challenges/06-pagination?page=1> を開き、画面下部のバー右側の **`○ms`（実行時間）** と、`Queries` タブに `COUNT(*)` が含まれていることをメモする。
2. 次に URL の末尾を `?page=24000` に変えて <http://localhost/challenges/06-pagination?page=24000> を開き、同じく `○ms` をメモする。**深いページのほうが遅い**こと（OFFSET 479980 ぶんを読み飛ばしている）を確認する。
3. `app/Http/Controllers/Challenges/PaginationController.php` を開き、`paginate()` を `cursorPaginate()` に置き換える（解答例は下記）。
4. ファイルを保存して**再読み込み**し、ページ下部の「次へ」リンクをたどって深いページまで進んでも、`○ms` が一定のままであることを確認する。

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
