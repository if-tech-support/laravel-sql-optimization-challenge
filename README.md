# Laravel SQL最適化チャレンジ

大量レコード（数十万件）の環境で「**効率的なクエリを書く力**」を磨くための研修課題リポジトリです。
EC サイトを題材に、バックエンドエンジニアが必ず押さえておきたい SQL 最適化トピックを 6 つの課題で扱います。

| # | テーマ | キーワード |
|---|--------|-----------|
| 01 | 必要なカラムだけ取得する | `SELECT *` 回避 / `select()` |
| 02 | N+1 問題の解消 | Eager Loading / `with()` / `withCount()` |
| 03 | LEFT JOIN で 0 件側を残す | `leftJoin` / `withCount` / INNER との違い |
| 04 | 集計は DB にやらせる | `GROUP BY` / `SUM` / `withSum` |
| 05 | インデックス設計 | `EXPLAIN` / 複合インデックス / フルスキャン |
| 06 | 大量データのページネーション | offset の限界 / `cursorPaginate` / `chunkById` |

各課題は「**動くが遅い実装**」が用意されています。これを計測し、最適化するのがゴールです。

---

## 技術スタック

- PHP 8.3+ / Laravel 13
- MySQL 8（Docker / Laravel Sail）
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)（クエリ数・実行時間の計測用）

## 前提

- **Docker Desktop** がインストール・起動済みであること（Sail を使うため）

---

## セットアップ

```bash
# 1. リポジトリを取得
git clone <このリポジトリのURL>
cd laravel-sql-optimization-challenge

# 2. 環境変数ファイルを用意
cp .env.example .env

# 3. 依存パッケージを取得（Sail を使うための一時コンテナ）
docker run --rm -v "$(pwd)":/var/www/html -w /var/www/html laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

# 4. コンテナを起動
./vendor/bin/sail up -d

# 5. アプリケーションキーを生成
./vendor/bin/sail artisan key:generate

# 6. マイグレーション ＆ 大量データ投入（数分かかります）
./vendor/bin/sail artisan migrate --seed
```

起動後、ブラウザで <http://localhost> を開くと課題一覧が表示されます。

> **データ量について**: 既定では users 1万 / products 5千 / orders 50万 / order_items 約100万 / reviews 20万件を投入します（投入は十数秒〜30秒程度）。
> マシンが非力で時間がかかりすぎる場合は `database/seeders/DatabaseSeeder.php` 上部の定数を縮小してください（課題の本質は変わりません）。

---

## 進め方

1. <http://localhost> から課題ページを開く。
2. まず**最適化前の状態**を計測する（クエリ本数・実行時間・EXPLAIN）。計測方法は **[docs/how-to-measure.md](docs/how-to-measure.md)** を参照。
3. 対応する `docs/0X-*.md` の手順に沿ってコードを改善する。
4. 再計測し、**Before/After を数字で**確認する。
5. 「なぜ速くなったか」を自分の言葉で説明できる状態にする。

> 各 `docs/0X-*.md` の末尾に「解答例」を折りたたみで入れています。**まず自力で挑戦**し、詰まったら開いてください。

### 課題ドキュメント

- [docs/how-to-measure.md](docs/how-to-measure.md) … 計測方法（Debugbar / クエリログ / EXPLAIN）【最初に読む】
- [docs/01-select.md](docs/01-select.md)
- [docs/02-nplus1.md](docs/02-nplus1.md)
- [docs/03-left-join.md](docs/03-left-join.md)
- [docs/04-aggregation.md](docs/04-aggregation.md)
- [docs/05-indexing.md](docs/05-indexing.md)
- [docs/06-pagination.md](docs/06-pagination.md)

---

## 提出方法（Pull Request）

この課題は **PR で提出**します。GitHub の操作にも慣れましょう。

```bash
# main から作業ブランチを切る
git switch -c feature/sql-optimization

# 課題ごとにコミット（粒度はテーマ単位で）
git add .
git commit -m "fix(01): SELECT * を必要カラムのみに最適化"

# リモートへ push
git push -u origin feature/sql-optimization
```

push 後、GitHub 上で Pull Request を作成してください。

### PR に含めてほしいこと

- 各課題で**何を・なぜ変えたか**の説明
- **Before/After の数字**（クエリ本数、実行時間、課題05 は EXPLAIN の `type`/`rows`）
- 詰まった点・調べたこと

---

## ディレクトリ構成（課題に関係する部分）

```
app/
  Http/Controllers/Challenges/   # 各課題の「遅い実装」（ここを最適化する）
  Models/                        # Category / Product / Order / OrderItem / Review
database/
  migrations/                    # スキーマ（インデックスは課題05のためあえて未設定）
  factories/                     # テスト用ファクトリ
  seeders/DatabaseSeeder.php     # 大量データ投入（バルク INSERT）
resources/views/challenges/      # 各課題の表示ビュー
routes/web.php                   # 課題ページのルート
docs/                            # 課題説明と計測ガイド
```

## データモデル

```
categories 1 ──< products >── n order_items >── 1 orders >── 1 users
                      │                                          │
                      └──< reviews >── 1 users  (reviews は users とも紐づく)
```

- `products.category_id` → `categories.id`
- `orders.user_id` → `users.id`
- `order_items.order_id` → `orders.id`、`order_items.product_id` → `products.id`
- `reviews.product_id` → `products.id`、`reviews.user_id` → `users.id`

> 外部キー制約・インデックスは**あえて張っていません**（課題05 で自分で設計するため）。
