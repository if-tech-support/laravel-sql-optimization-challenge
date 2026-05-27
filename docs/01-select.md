# 課題01: 必要なカラムだけ取得する（SELECT * を避ける）

## 背景

`Model::all()` や `Model::get()` は、何も指定しなければ `SELECT *`（全カラム）を発行します。
画面で使うのは一部のカラムだけなのに、`description` のような重いテキスト列まで毎回 DB から転送し、
PHP のメモリにモデルとして展開するのは無駄です。行数が増えるほど効いてきます。

## 対象

- ページ: <http://localhost/challenges/01-select>
- コントローラ: `app/Http/Controllers/Challenges/SelectController.php`
- ビュー: `resources/views/challenges/select.blade.php`

このページは商品の **id / name / price** しか表示しません。

## やること

1. まず現状のページを開き、Debugbar で実行時間とメモリを確認する。
2. 取得カラムを、実際に使う `id` / `name` / `price` だけに絞る。
3. 再度開いて、転送量・メモリが減ることを確認する。

## 達成基準

- 発行される SQL が `SELECT *` ではなく、必要カラムのみの `SELECT id, name, price ...` になっている。
- 画面表示は変わらない。

## ヒント

- `select()` メソッド、または `get(['id', 'name', 'price'])` の引数で絞れます。
- 「使うカラムだけ取る」は、後続の課題（N+1 解消後の Eager Load でも `select` できる）にも通じる基本です。

<details>
<summary>解答例（自分で挑戦してから開く）</summary>

```php
$products = Product::query()
    ->select(['id', 'name', 'price'])
    ->orderBy('id')
    ->limit(1000)
    ->get();
```

</details>
