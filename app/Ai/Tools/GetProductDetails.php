<?php

namespace App\Ai\Tools;

use App\Models\Product;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProductDetails implements Tool
{
    /**
     * Mô tả chức năng để AI chọn gọi khi cần thông tin chi tiết của một sản phẩm.
     */
    public function description(): Stringable|string
    {
        return 'Lấy thông tin chi tiết của một sản phẩm cụ thể theo ID của sản phẩm. Sử dụng công cụ này khi khách hàng yêu cầu chi tiết về thông số, giá cả, hoặc tồn kho của một sản phẩm.';
    }

    /**
     * Truy vấn thông tin sản phẩm và trả về định dạng JSON thô cho AI tự tổng hợp câu trả lời.
     */
    public function handle(Request $request): Stringable|string
    {
        $product = Product::with('category')->find($request['product_id']);

        if (! $product) {
            return 'Không tìm thấy sản phẩm.';
        }

        return json_encode([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'description' => $product->description,
            'category' => $product->category?->name,
        ]);
    }

    /**
     * Cấu trúc tham số truyền vào công cụ.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'product_id' => $schema->integer('ID của sản phẩm cần lấy thông tin chi tiết.')->required(),
        ];
    }
}
