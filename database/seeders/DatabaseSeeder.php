<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * 大量データ投入シーダー。
 *
 * Faker の個別生成は数十万件規模では遅いため、軽量な値生成 + chunk バルク INSERT で投入する。
 * （大量データは Model::factory()->create() の 1 件ずつではなく insert で入れるのが実務的にも正解）
 *
 * マシンが非力で重い場合は下記の定数を縮小してよい（課題の本質は変わらない）。
 */
class DatabaseSeeder extends Seeder
{
    private const CATEGORIES = 50;
    private const USERS = 10_000;
    private const PRODUCTS = 5_000;
    private const ORDERS = 100_000;
    private const MAX_ITEMS_PER_ORDER = 4;
    private const REVIEWS = 50_000;

    /** バルク INSERT の 1 回あたり件数（プレースホルダ上限に注意して 2000 程度に） */
    private const CHUNK = 2_000;

    public function run(): void
    {
        $now = now()->format('Y-m-d H:i:s');
        $baseTs = now()->getTimestamp();
        $twoYears = 60 * 60 * 24 * 730;

        $this->command->info('Seeding categories ...');
        $rows = [];
        for ($i = 1; $i <= self::CATEGORIES; $i++) {
            $rows[] = [
                'name' => 'Category ' . $i,
                'slug' => 'category-' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('categories')->insert($rows);

        $this->command->info('Seeding users ...');
        $password = Hash::make('password'); // 全ユーザー共通パスワード（ログイン用途は想定しない）
        $this->insertInChunks(self::USERS, function (int $i) use ($now, $password) {
            return [
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'email_verified_at' => $now,
                'password' => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, 'users');

        $this->command->info('Seeding products ...');
        $this->insertInChunks(self::PRODUCTS, function (int $i) use ($now) {
            return [
                'category_id' => random_int(1, self::CATEGORIES),
                'name' => 'Product ' . $i . ' ' . Str::random(6),
                'sku' => 'SKU-' . str_pad((string) $i, 7, '0', STR_PAD_LEFT),
                'price' => random_int(300, 50000),
                'stock' => random_int(0, 500),
                'description' => 'Sample description for product ' . $i . '.',
                'is_published' => random_int(1, 100) <= 90 ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, 'products');

        $this->command->info('Seeding orders ...');
        $statuses = ['pending', 'paid', 'shipped', 'cancelled'];
        $this->insertInChunks(self::ORDERS, function (int $i) use ($statuses, $baseTs, $twoYears) {
            $orderedAt = date('Y-m-d H:i:s', $baseTs - random_int(0, $twoYears));

            return [
                'user_id' => random_int(1, self::USERS),
                'status' => $statuses[array_rand($statuses)],
                'ordered_at' => $orderedAt,
                'created_at' => $orderedAt,
                'updated_at' => $orderedAt,
            ];
        }, 'orders');

        $this->command->info('Seeding order_items ...');
        $this->seedOrderItems($now);

        $this->command->info('Seeding reviews ...');
        $this->insertInChunks(self::REVIEWS, function (int $i) use ($baseTs, $twoYears) {
            $createdAt = date('Y-m-d H:i:s', $baseTs - random_int(0, $twoYears));

            return [
                'product_id' => random_int(1, self::PRODUCTS),
                'user_id' => random_int(1, self::USERS),
                'rating' => random_int(1, 5),
                'comment' => random_int(1, 100) <= 70 ? 'Review comment ' . $i : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }, 'reviews');

        $this->command->info('Done. テストデータの投入が完了しました。');
    }

    /**
     * $count 件のレコードを $rowFactory で生成し、CHUNK 単位でバルク INSERT する。
     *
     * @param  callable(int):array<string, mixed>  $rowFactory
     */
    private function insertInChunks(int $count, callable $rowFactory, string $table): void
    {
        $buffer = [];
        for ($i = 1; $i <= $count; $i++) {
            $buffer[] = $rowFactory($i);
            if (count($buffer) >= self::CHUNK) {
                DB::table($table)->insert($buffer);
                $buffer = [];
            }
        }
        if ($buffer !== []) {
            DB::table($table)->insert($buffer);
        }
    }

    /**
     * 各注文に 1〜MAX_ITEMS_PER_ORDER 件の明細をぶら下げる。
     */
    private function seedOrderItems(string $now): void
    {
        $buffer = [];
        for ($orderId = 1; $orderId <= self::ORDERS; $orderId++) {
            $itemCount = random_int(1, self::MAX_ITEMS_PER_ORDER);
            for ($n = 0; $n < $itemCount; $n++) {
                $buffer[] = [
                    'order_id' => $orderId,
                    'product_id' => random_int(1, self::PRODUCTS),
                    'quantity' => random_int(1, 5),
                    'unit_price' => random_int(300, 50000),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (count($buffer) >= self::CHUNK) {
                DB::table('order_items')->insert($buffer);
                $buffer = [];
            }
        }
        if ($buffer !== []) {
            DB::table('order_items')->insert($buffer);
        }
    }
}
