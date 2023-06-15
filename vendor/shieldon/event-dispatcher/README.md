# PHP Event Dispatcher

[![Build Status](https://travis-ci.org/terrylinooo/event-dispatcher.svg?branch=master)](https://travis-ci.org/terrylinooo/event-dispatcher) [![codecov](https://codecov.io/gh/terrylinooo/event-dispatcher/branch/master/graph/badge.svg)](https://codecov.io/gh/terrylinooo/event-dispatcher) [![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

This package is designed as a part of [Shieldon Firewall 2](https://github.com/terrylinooo/shieldon). You can also use it on your projects as well.

## Installation

Use PHP Composer:

```php
composer require shieldon/event-dispatcher
```

Or, download it and include the Shieldon autoloader.
```php
require 'autoload.php';
```

## Usage

### Add a Listener
```php
/**
 * @param string        $name      The name of an event.
 * @param string|array  $func      Callable function or class.
 * @param int           $priority  The execution priority.
 * 
 * @return bool
 */
\Shieldon\Event\Event::addLister(string $name, $func, int $priority = 10): bool
```

Please note, the **priority** must be **unique**. This method returns true when add a listener, false when the prirotiy has been taken by another listener.

### Dispatch

```php
/**
 * @param string $name The name of an event.
 * @param array  $args The arguments.
 * 
 * @return mixed
 */
\Shieldon\Event\Event::doDispatch(string $name, array $args = []): mixed
```

Return the filtered result, it's similar to WordPress' filter. You can ignore the return if you don't need that.


## Example


### Closure

Add a listener.

```php
\Shieldon\Event\Event::addListener('test_1', function() {
    echo 'This is a closure function call.';
});
```

Dispatch.

```php
$result = \Shieldon\Event\Event::doDispatch('test_1');
```

### Function

Function for listener.
```php
function test_event_disptcher()
{
    echo 'This is a function call.';
}
```

Add a listener.

```php
\Shieldon\Event\Event::addListener('test_2', 'test_event_disptcher');
```

Dispatch.

```php
$result = \Shieldon\Event\Event::doDispatch('test_2');
```

### Class

Add a listener.

```php
$example = new Example();

\Shieldon\Event\Event::addListener('test_3', [$example, 'example1']);
```

Dispatch.

```php
$result = \Shieldon\Event\Event::doDispatch('test_3');
```

Hope this helps.

---

#### Author

- [Terry L.](https://terryl.in)

#### License

MIT
