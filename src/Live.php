<?php

declare(strict_types=1);

namespace Termwind\Live;

use Closure;
use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\SignalRegistry\SignalRegistry;
use Termwind\Components\Element;
use Termwind\HtmlRenderer;
use Termwind\Live\Events\RefreshEvent;

/**
 * @internal
 */
final class Live
{
    private Cursor $cursor;

    private SignalRegistry $signals;

    private string $lastContent = '';

    /**
     * Creates a new Live instance.
     */
    public function __construct(
        private ConsoleSectionOutput $output,
        private HtmlRenderer $renderer,
        private Closure $htmlResolver
    ) {
        $this->cursor = new Cursor($output);
        $this->signals = new SignalRegistry();

        $this->registerSignals([SIGINT, SIGTERM], function (): void {
            $this->showCursor();
            exit;
        });
    }

    /**
     * Clears the html.
     */
    public function clear(): self
    {
        $this->output->clear();

        return $this;
    }

    /**
     * Freezes the terminal for the given amount of milliseconds.
     */
    public function freeze(int $milliseconds): self
    {
        usleep($milliseconds * 1000);

        return $this;
    }

    /**
     * Hides the cursor.
     */
    public function hideCursor(): self
    {
        $this->cursor->hide();

        return $this;
    }

    /**
     * Shows the cursor.
     */
    public function showCursor(): self
    {
        $this->cursor->clearOutput();
        $this->cursor->show();

        return $this;
    }

    /**
     * Overwrites the content.
     */
    public function overwrite(Element $html): bool
    {
        $html = $html->toString();

        if ($this->lastContent === $html) {
            return false;
        }

        $this->output->clear();
        $this->output->write($html);

        $this->lastContent = $html;

        return true;
    }

    /**
     * Clears the html, and re-renders the live html.
     */
    public function refresh(): self
    {
        $this->render();

        return $this;
    }

    /**
     * Refreshes the content by the amount provided.
     *
     * @return $this
     */
    public function refreshEvery(int $milliseconds = 0, int $seconds = 0): self
    {
        while (true) {
            $this->freeze($milliseconds + $seconds * 1000);

            $html = call_user_func($this->htmlResolver, $refreshingEvent = new RefreshEvent());

            if ($refreshingEvent->stop) {
                break;
            }

            $html = $this->renderer->parse((string) $html);

            $this->overwrite($html);
        }

        return $this;
    }

    /**
     * Refreshes the content every amount of seconds.
     *
     * @return $this
     */
    public function refreshEverySeconds(int $seconds): self
    {
        return $this->refreshEvery(seconds: $seconds);
    }

    /**
     * Refreshes the content every amount of milliseconds.
     *
     * @return $this
     */
    public function refreshEveryMilliseconds(int $milliseconds): self
    {
        return $this->refreshEvery(milliseconds: $milliseconds);
    }

    /**
     * Renders the live html.
     */
    public function render(): bool
    {
        $html = call_user_func($this->htmlResolver, $refreshingEvent = new RefreshEvent());

        $html = $this->renderer->parse((string) $html);

        return $this->overwrite($html);
    }

    /**
     * Refreshs the content while the condition is `true`.
     */
    public function while(Closure $callback): self
    {
        while (true) {
            if (! $callback()) {
                break;
            }

            $this->refresh();
        }

        return $this;
    }

    /**
     * Registers the console signals.
     *
     * @param  array<int, int>  $signals
     */
    private function registerSignals(array $signals, Closure $callback): void
    {
        foreach ($signals as $signal) {
            $this->signals->register($signal, $callback);
        }
    }
}
