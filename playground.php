<?php

require_once __DIR__.'/vendor/autoload.php';

use function Termwind\Live\live;

$total = 0;

live(function () use (&$total) {
    return sprintf('The content was refreshed %d times.', ++$total);
})->refreshEvery(1);
