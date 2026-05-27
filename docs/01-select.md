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

> Debugbar の見方が分からない場合は、先に [docs/how-to-measure.md](how-to-measure.md) を読んでください。

1. ブラウザで <http://localhost/challenges/01-select> を開く。
2. 画面下部のバーの **右側の `○MB`（メモリ）と `○ms`（時間）** をメモする。さらにバーの **`Models` タブ**をクリックし、展開された件数（例: `Models (1000)`）も控える。← これが Before。
3. `app/Http/Controllers/Challenges/SelectController.php` を開き、取得カラムを実際に使う `id` / `name` / `price` だけに絞る（解答例は下記）。
4. ファイルを保存し、**ブラウザを再読み込み**（`⌘ + R` / `Ctrl + R`）する。
5. バー右側の `○MB`（メモリ）が Before より減っていることを確認する。画面の表示内容（ID・商品名・価格）は変わらないこともあわせて確認する。

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
