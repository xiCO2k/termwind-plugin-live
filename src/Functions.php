<?php

declare(strict_types=1);

namespace Termwind\Live;

use Closure;
use Symfony\Component\Console\Output\ConsoleOutput;
use Termwind\HtmlRenderer;
use Termwind\Live\Exceptions\InvalidRenderer;
use Termwind\Termwind;

if (! function_exists('Termwind\Live\live')) {
    /**
     * Render HTML to a string, and keeps the html live.
     */
    function live(Closure|string $htmlResolver): Live
    {
        $output = Termwind::getRenderer();

        if (! $output instanceof ConsoleOutput) {
            throw new InvalidRenderer(
                'The renderer must be an instance of Symfony\'s ConsoleOutput',
            );
        }

        if (is_string($htmlResolver)) {
            $htmlResolver = fn () => $htmlResolver;
        }

        $live = new Live($output->section(), new HtmlRenderer(), $htmlResolver);

        $live->render();

        return $live;
    }
}
