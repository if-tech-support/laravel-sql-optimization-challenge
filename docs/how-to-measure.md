# 計測のやり方（共通）

最適化は「**速くなったかを必ず数字で確認する**」のが大原則です。
このドキュメントでは、各課題で使う 3 つの計測手段を説明します。

> 体感ではなく「クエリ本数」「実行時間」「EXPLAIN」で語れるようになることが、この課題のゴールです。

---

## 1. Debugbar（ブラウザで見る）

このリポジトリには [barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar) が入っています。
`APP_DEBUG=true`（`.env` の初期値）であれば、各ページの下部にバーが表示されます。

- **Queries タブ**: 発行された SQL の一覧と本数。N+1 はここで「同じ形のクエリが何百本も並ぶ」現象として見えます。
- **タイミング（実行時間）**: ページ生成にかかった時間。
- 各クエリの実行時間も個別に表示されます。

> 課題02 / 04（N+1）は **クエリ本数**、課題01 / 05 / 06 は主に **実行時間** に注目してください。

---

## 2. クエリログ（コードで取る）

`tinker` やコントローラ内で、発行クエリを配列として取得できます。

```bash
./vendor/bin/sail artisan tinker
```

```php
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

// 計測したい処理
App\Models\Order::with('user')->limit(100)->get();

$log = DB::getQueryLog();
count($log);   // 発行されたクエリ本数
$log[0];       // 1 本目の SQL とバインド値
```

実行時間をざっくり測るなら：

```php
$start = microtime(true);
// 計測したい処理
$ms = (microtime(true) - $start) * 1000;
printf("%.1f ms\n", $ms);
```

---

## 3. EXPLAIN（インデックスの効きを見る）

クエリがインデックスを使えているか、フルスキャンになっていないかは `EXPLAIN` で確認します。
課題05 で必須です。

ビルダの生 SQL を取り出して MySQL で EXPLAIN します。

```php
$query = App\Models\Order::where('user_id', 1234)->where('status', 'paid');

DB::select('EXPLAIN ' . $query->toSql(), $query->getBindings());
```

または MySQL に直接入って実行します。

```bash
./vendor/bin/sail mysql
```

```sql
EXPLAIN SELECT * FROM orders WHERE user_id = 1234 AND status = 'paid' ORDER BY ordered_at DESC;
```

### EXPLAIN の読みどころ

| 列 | 注目ポイント |
|----|------|
| `type` | `ALL` = フルスキャン（遅い）。`ref` / `range` / `const` に近いほど良い。 |
| `key` | 実際に使われたインデックス。`NULL` ならインデックス未使用。 |
| `rows` | 走査見込み行数。小さいほど良い。 |
| `Extra` | `Using filesort` / `Using temporary` が出たら並び替え・集計の改善余地あり。 |

> インデックスを張る前後で `type` と `rows` がどう変わるかを、必ずスクショかメモで残しておきましょう。PR の説明に貼ると説得力が出ます。
