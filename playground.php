<?php

require_once __DIR__.'/vendor/autoload.php';

use function Termwind\Live\live;

live(function () {
    static $total = 0;

    return sprintf('The content was refreshed %d times.', ++$total);
})->refreshEvery(1);
