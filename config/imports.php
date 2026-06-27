<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stories import paths
    |--------------------------------------------------------------------------
    |
    | These point at raw filesystem locations under storage/app rather than at
    | the "local" disk, whose root is storage/app/private. The import tree has
    | one folder per author, each containing a stories_metadata.csv and the
    | Word documents referenced by that CSV.
    |
    */

    'root' => storage_path('app/imports'),

    'log_dir' => storage_path('app/import_logs'),

    'not_found_log' => 'not_found_stories.md',

    'csv_name' => 'stories_metadata.csv',

];
