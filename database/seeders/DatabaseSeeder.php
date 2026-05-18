<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Profile;
use App\Models\ProductCategory;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin Demo',
            'role' => 'admin',
            'is_active' => true,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::query()->updateOrCreate([
            'email' => 'support@example.com',
        ], [
            'name' => 'Support Lead',
            'role' => 'editor',
            'is_active' => true,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $extraUsersNeeded = max(0, 10 - User::count());

        if ($extraUsersNeeded > 0) {
            User::factory($extraUsersNeeded)->create();
        }

        foreach (User::all() as $user) {
            Profile::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => $user->name,
                    'address' => 'Dia chi cua '.$user->name,
                    'avatar' => null,
                    'birthday' => now()->subYears(rand(18, 30))->toDateString(),
                    'gender' => ['Nam', 'Nu', 'Khac'][array_rand(['Nam', 'Nu', 'Khac'])],
                    'phone' => '090'.str_pad((string) rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                ]
            );
        }

        $editor = User::where('email', 'support@example.com')->first();

        foreach ([
            ['name' => 'Laptop Gaming', 'slug' => 'laptop-gaming', 'description' => 'Danh muc cho laptop hieu nang cao.', 'is_active' => true],
            ['name' => 'Phu kien Van phong', 'slug' => 'phu-kien-van-phong', 'description' => 'Chuot, ban phim va thiet bi van phong.', 'is_active' => true],
            ['name' => 'Do gia dung thong minh', 'slug' => 'do-gia-dung-thong-minh', 'description' => 'San pham smart home va tu dong hoa.', 'is_active' => false],
        ] as $category) {
            ProductCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'created_by' => $editor?->id]
            );
        }

        foreach ([
            ['name' => 'Laptop Predator X', 'slug' => 'laptop-predator-x', 'sku' => 'LP-001', 'price' => 2499.99, 'stock' => 8, 'description' => 'Laptop gaming cao cap cho editor.', 'category_slug' => 'laptop-gaming', 'is_active' => true],
            ['name' => 'Ban phim Co Pro', 'slug' => 'ban-phim-co-pro', 'sku' => 'KB-002', 'price' => 129.50, 'stock' => 25, 'description' => 'Ban phim co cho van phong va streaming.', 'category_slug' => 'phu-kien-van-phong', 'is_active' => true],
            ['name' => 'Den thong minh Mini', 'slug' => 'den-thong-minh-mini', 'sku' => 'SM-003', 'price' => 49.90, 'stock' => 14, 'description' => 'Den smart home dieu khien bang app.', 'category_slug' => 'do-gia-dung-thong-minh', 'is_active' => false],
        ] as $product) {
            $category = ProductCategory::where('slug', $product['category_slug'])->first();

            if (! $category) {
                continue;
            }

            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'product_category_id' => $category->id,
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'description' => $product['description'],
                    'is_active' => $product['is_active'],
                    'created_by' => $editor?->id,
                ]
            );
        }

        $existingTagCount = Tag::count();
        if ($existingTagCount < 20) {
            Tag::factory(20 - $existingTagCount)->create();
        }

        $existingArticleCount = Article::count();
        if ($existingArticleCount < 50) {
            Article::factory(50 - $existingArticleCount)->create();
        }

        $tagIds = Tag::query()->pluck('id')->all();

        Article::with('tags')->get()->each(function (Article $article) use ($tagIds): void {
            if ($tagIds === []) {
                return;
            }

            $shuffled = $tagIds;
            shuffle($shuffled);

            $attachIds = array_slice($shuffled, 0, min(10, count($shuffled)));

            $article->tags()->syncWithoutDetaching($attachIds);
        });

        $products = Product::query()->where('is_active', true)->get();

        if ($products->isNotEmpty()) {
            $targetOrders = 25;

            while (Order::count() < $targetOrders) {
                $sequence = Order::count() + 1;
                $placedAt = Carbon::now()->subDays(rand(0, 20))->subHours(rand(0, 23));
                $order = Order::create([
                    'order_number' => 'ORD-'.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT),
                    'customer_name' => fake()->name(),
                    'customer_email' => fake()->unique()->safeEmail(),
                    'customer_phone' => '09'.rand(10000000, 99999999),
                    'customer_address' => fake()->address(),
                    'notes' => fake()->boolean(40) ? fake()->sentence() : null,
                    'status' => fake()->randomElement(Order::STATUSES),
                    'total_amount' => 0,
                    'placed_at' => $placedAt,
                ]);

                $selectedProducts = $products->shuffle()->take(rand(1, min(4, $products->count())));
                $totalAmount = 0;

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);
                    $lineTotal = $quantity * (float) $product->price;
                    $totalAmount += $lineTotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'line_total' => $lineTotal,
                    ]);
                }

                $order->update(['total_amount' => $totalAmount]);
            }
        }
    }
}
