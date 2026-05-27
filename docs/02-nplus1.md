# 課題02: N+1 問題を解消する（Eager Loading）

## 背景

一覧を取得したあと、各レコードのリレーション（`$order->user` など）をループ内で参照すると、
**1 件ごとに追加のクエリ**が飛びます。注文 100 件なら「注文取得 1 本 + ユーザー取得 100 本 + 明細取得 100 本」と
クエリが膨れ上がります。これが N+1 問題です。データ量が増えるほど致命的になります。

## 対象

- ページ: <http://localhost/challenges/02-nplus1>
- コントローラ: `app/Http/Controllers/Challenges/NPlusOneController.php`
- ビュー: `resources/views/challenges/nplus1.blade.php`（ここで `$order->user->name` と `$order->items` を参照）

## やること

> Debugbar の見方が分からない場合は、先に [docs/how-to-measure.md](how-to-measure.md) を読んでください。

1. ブラウザで <http://localhost/challenges/02-nplus1> を開く。
2. 画面下部のバーの **`Queries` タブ**をクリックする。タブのカッコ内の数字が **クエリ本数**で、ここが **200 本超**になっているはず。クリックして開くと、`select * from users where id = ?` のような **同じ形の SQL が延々と並ぶ**のが見える ← これが N+1 の正体。
3. `app/Http/Controllers/Challenges/NPlusOneController.php` を開き、必要なリレーションを Eager Load する（解答例は下記）。
4. ファイルを保存し、**ブラウザを再読み込み**（`⌘ + R` / `Ctrl + R`）する。
5. `Queries` タブの本数が **3 本程度**まで激減していることを確認する。画面の表示内容は変わらないこともあわせて確認する。

## 達成基準

- クエリ本数が **3 本程度**まで減っている。
- 画面表示は変わらない。

## ヒント

- `with(['user', 'items'])` を付けるのが基本。
- 「件数だけ」が欲しい `$order->items->count()` は、明細の中身が不要なら `withCount('items')` にするとさらに軽くなります（コレクションを展開せず DB で数える）。
- Eager Load 時にも `with(['user:id,name'])` のようにカラムを絞れます（課題01 の応用）。

<details>
<summary>解答例（自分で挑戦してから開く）</summary>

```php
// コントローラ
$orders = Order::query()
    ->with(['user:id,name'])
    ->withCount('items')      // $order->items_count で参照
    ->orderByDesc('ordered_at')
    ->limit(100)
    ->get();
```

```blade
{{-- ビュー: items->count() の代わりに items_count を使う --}}
<td>{{ $order->items_count }}</td>
```

</details>
