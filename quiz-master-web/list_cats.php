<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Category::all() as $cat) {
    echo $cat->id . "|" . $cat->name . "|" . $cat->icon_class . PHP_EOL;
}
