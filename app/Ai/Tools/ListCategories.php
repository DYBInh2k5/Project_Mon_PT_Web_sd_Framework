<?php

namespace App\Ai\Tools;

use App\Models\ProductCategory;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListCategories implements Tool
{
    /**
     * Mô tả chức năng để AI chọn gọi khi người dùng muốn biết danh sách danh mục.
     */
    public function description(): Stringable|string
    {
        return 'Liệt kê tất cả các danh mục sản phẩm hiện có. Sử dụng công cụ này khi khách hàng hỏi về các danh mục hoặc muốn duyệt sản phẩm theo nhóm danh mục.';
    }

    /**
     * Truy vấn toàn bộ danh mục sản phẩm đang hoạt động kèm số lượng sản phẩm liên kết.
     */
    public function handle(Request $request): Stringable|string
    {
        $categories = ProductCategory::withCount('products')->get(['id', 'name', 'description']);

        return $categories->toJson();
    }

    /**
     * Công cụ này không yêu cầu bất kỳ tham số đầu vào nào từ AI.
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
