<?php

namespace App\Ai\Tools;

use App\Models\Product;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchProducts implements Tool
{
    /**
     * Mô tả chức năng của Tool để AI biết khi nào nên chọn gọi công cụ này.
     */
    public function description(): Stringable|string
    {
        return 'Tìm kiếm sản phẩm theo tên. Sử dụng công cụ này khi khách hàng hỏi về một sản phẩm cụ thể hoặc muốn tìm kiếm các sản phẩm trong hệ thống.';
    }

    /**
     * Logic xử lý thực tế của Tool khi được AI gọi.
     * Nhận request đầu vào từ AI và trả về kết quả dưới dạng chuỗi JSON.
     */
    public function handle(Request $request): Stringable|string
    {
        // Truy vấn danh sách sản phẩm khớp với từ khóa tìm kiếm.
        $products = Product::with('category')
            ->where('name', 'like', '%' .  $request['query']  . '%')
            ->limit(10)
            ->get(['id', 'name', 'price', 'stock', 'product_category_id']);

        if ($products->isEmpty()) {
            return 'Không tìm thấy sản phẩm nào khớp với từ khóa "' . $request['query'] . '".';
        }

        return $products->toJson();
    }

    /**
     * Định nghĩa cấu trúc dữ liệu đầu vào (JSON Schema) mà AI cần cung cấp khi gọi Tool này.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string('Từ khóa tìm kiếm tên sản phẩm.')->required(),
        ];
    }
}
