<?php

declare(strict_types=1);

namespace Termwind\Live;

use Closure;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Termwind\HtmlRenderer;
use Termwind\Live\Events\RefreshEvent;

/**
 * @internal
 */
final class Live
{
    /**
     * Creates a new Live instance.
     */
    public function __construct(
        private ConsoleSectionOutput $output,
        private HtmlRenderer $renderer,
        private Closure $htmlResolver
    ) {
        // ..
    }

    /**
     * Clears the html.
     */
    public function clear(): void
    {
        $this->output->clear();
    }

    /**
     * Renders the live html.
     */
    public function render(): bool
    {
        $html = call_user_func($this->htmlResolver, $refreshingEvent = new RefreshEvent());

        $html = $this->renderer->parse((string) $html);

        $this->output->write($html->toString());

        return true;
    }

    /**
     * Clears the html, and re-renders the live html.
     */
    public function refresh(): void
    {
        $this->output->clear();

        $this->render();
    }

    /**
     * Freezes the terminal for the given amount of seconds.
     */
    public function freeze(int $seconds): void
    {
        sleep($seconds);
    }

    /**
     * Creates a new Refreshable Live instance.
     *
     * @return $this
     */
    public function refreshEvery(int $seconds): self
    {
        while (true) {
            $this->freeze($seconds);

            $html = call_user_func($this->htmlResolver, $refreshingEvent = new RefreshEvent());

            if ($refreshingEvent->stop) {
                break;
            }

            $this->output->clear();

            $html = $this->renderer->parse((string) $html);

            $this->output->write($html->toString());
        }

        return $this;
    }
}
