<p align="center">
    <img width="150" height="150" alt="Termwind logo" src="/art/logo.png"/>
</p>

<h1 align="center" style="border:none !important">
    <code>Termwind Live Plugin</code>
    <br>
    <br>
</h1>

<p align="center">
    <p align="center">
        <a href="https://github.com/xico2k/termwind-plugin-live/actions"><img alt="GitHub Workflow Status (master)" src="https://img.shields.io/github/workflow/status/xico2k/termwind-plugin-live/Tests/master"></a>
        <a href="https://packagist.org/packages/xico2k/termwind-plugin-live"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/xico2k/termwind-plugin-live"></a>
        <a href="https://packagist.org/packages/xico2k/termwind-plugin-live"><img alt="Latest Version" src="https://img.shields.io/packagist/v/xico2k/termwind-plugin-live"></a>
        <a href="https://packagist.org/packages/xico2k/termwind-plugin-live"><img alt="License" src="https://img.shields.io/packagist/l/xico2k/termwind-plugin-live"></a>
    </p>
</p>


------
**Termwind Live Plugin** allows you to make your cli content live.

## Installation

> **Requires [PHP 8.0+](https://php.net/releases/)**

Require Termwind Live Plugin using [Composer](https://getcomposer.org):

```bash
composer require xico2k/termwind-plugin-live
```

## Usage

```php
use function Termwind\Live\live;

live(function () {
    static $total = 0;

    return sprintf('The content was refreshed %d times.', ++$total);
})->refreshEvery(seconds: 1);
```

### `refreshEvery(milliseconds: 0, seconds: 0)`

The `refreshEvery()` method may be used to update the content by
certain amount of time.

```php
// Milliseconds
live(fn () => 'foo')->refreshEvery(milliseconds: 250);

// Seconds
live(fn () => 'foo')->refreshEvery(seconds: 2);
```

### `while(Closure $condition)`

The `while()` method may be used to update the content
while the condition is `true`.

```php
live(fn () => 'Loading...')
    ->while(fn () => $process->running());
```

### `showCursor()` and `hideCursor()`

The `showCursor()` and `hideCursor()` methods may be used to
show/hide the cursor on your cli.

```php
live('Loading...')
    ->hideCursor()
    ->while(fn () => $process->running())
    ->showCursor();
```

### `clear()`

The `clear()` method may be used to clear the live output.

```php
live('Loading...')
    ->while(fn () => $process->running())
    ->clear();
```

### `RefreshEvent`

The `RefreshEvent` is passed to the `Closure` and provides a way
to stop the execution.

```php
use Termwind\Live\Events\RefreshEvent;

live(function (RefreshEvent $event) {
    $shouldStop = true; // Call to check something...

    if ($shouldStop) {
        $event->stop();
    }

    return 'foo';
})->refreshEvery(seconds: 1);
```

---

Termwind Live Plugin is an open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.
