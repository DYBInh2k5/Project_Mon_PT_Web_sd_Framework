<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;

class SafeFilesystem extends Filesystem
{
    /**
     * Replace file contents without relying on rename().
     *
     * Windows in this project can deny atomic rename operations on cache files,
     * so we write the final file directly after ensuring the directory exists.
     */
    public function replace($path, $content, $mode = null)
    {
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;

        $this->ensureDirectoryExists(dirname($path));

        $this->put($path, $content, true);

        if (! is_null($mode)) {
            @chmod($path, $mode);
        }
    }
}
