<?php

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Termwind\Live\Events\RefreshEvent;
use Termwind\Live\Exceptions\InvalidRenderer;
use function Termwind\Live\live;
use function Termwind\renderUsing;

it('requires symfony console output', function () {
    renderUsing(new BufferedOutput());

    live(fn () => 'foo');
})->throws(InvalidRenderer::class);

it('renders the closure result', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->once()->with('foo');
    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(fn () => 'foo');
});

it('renders a string', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->once()->with('foo');
    $this->output->shouldReceive('section')->once()->andReturn($section);

    live('foo');
});

it('clears the previous closure result', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->once()->with('foo');
    $section->shouldReceive('clear')->times(2);
    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(fn () => 'foo')->clear();
});

it('re-renders the closure result', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->with('foo');
    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(fn () => 'foo')->render();
});

it('may be refreshed', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->once()->with('foo');
    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(fn () => 'foo')->refresh();
});

it('may be refreshed every X times', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->once()->with(1);
    $section->shouldReceive('write')->once()->with(2);
    $section->shouldReceive('write')->once()->with(3);
    $section->shouldReceive('write')->once()->with(4);
    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(function (RefreshEvent $event) {
        static $counter = 0;

        $counter++;

        if ($counter < 5) {
            return $counter;
        }

        $event->stop();
    })->refreshEvery(0);
});

it('may be refreshed every X of seconds', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->once()->with('refreshed');
    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(function (RefreshEvent $event) {
        $event->stop();

        return 'refreshed';
    })->refreshEverySeconds(0);
});

it('may be refreshed every X of milliseconds', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->once()->with('refreshed');
    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(function (RefreshEvent $event) {
        $event->stop();

        return 'refreshed';
    })->refreshEveryMilliseconds(0);
});

it('may be refreshed while the condition is true', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->times(4);
    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(fn () => rand())->while(function () {
        static $counter = 0;

        $counter++;

        if ($counter > 3) {
            return false;
        }

        return true;
    });
});

it('hides the cursor', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();
    $section->shouldReceive('write')->once()->with("\x1b[?25l");

    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(fn () => 'foo')->hideCursor();
});

it('shows the cursor', function () {
    $section = Mockery::mock(ConsoleSectionOutput::class)->shouldIgnoreMissing();

    $section->shouldReceive('write')->once()->with("\x1b[0J");
    $section->shouldReceive('write')->once()->with("\x1b[?25h\x1b[?0c");

    $this->output->shouldReceive('section')->once()->andReturn($section);

    live(fn () => 'foo')->showCursor();
});
