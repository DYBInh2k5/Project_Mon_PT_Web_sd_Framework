<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\ProductCategory;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
                    'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=465fff&color=ffffff&size=160',
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
    }
}
