<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Templates are loaded from these paths. The app only needs the default
    | resources/views directory, so we keep the config minimal and explicit.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | Store compiled Blade files outside the long Windows project path to
    | avoid rename/file-lock issues in storage/framework/views.
    |
    */

    'compiled' => base_path('cache/views'),

];
