<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$icons = App\Models\Category::distinct()->pluck('icon_class')->toArray();
foreach ($icons as $i) {
    echo $i . PHP_EOL;
}
