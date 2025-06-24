<?php declare(strict_types=1);

use App\Core\App;

require_once __DIR__ . "/../vendor/autoload.php";

try {
    App::run();
} catch (\Exception|Throwable $e) {
    die($e->getMessage());
}