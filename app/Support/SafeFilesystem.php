<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;

class SafeFilesystem extends Filesystem
{
    /**
     * Ghi đè nội dung file một cách an toàn mà không dựa vào hàm rename() nguyên tử mặc định.
     *
     * Hệ điều hành Windows trong một số môi trường có thể chặn thao tác đổi tên (rename) nguyên tử đối với các file cache,
     * do đó phương thức này ghi trực tiếp nội dung vào file sau khi chắc chắn thư mục cha đã tồn tại.
     */
    public function replace($path, $content, $mode = null)
    {
        // Xóa bộ nhớ đệm trạng thái của file để đảm bảo thông tin kích thước và thời gian sửa đổi luôn mới nhất
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;

        $this->ensureDirectoryExists(dirname($path));

        // Ghi nội dung mới trực tiếp vào file
        $this->put($path, $content, true);

        if (! is_null($mode)) {
            @chmod($path, $mode);
        }
    }
}
