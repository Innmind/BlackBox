# Getting started

## Installation

```sh
composer require --dev innmind/black-box
```

## Setup

```php title="blackbox.php"
<?php
declare(strict_types = 1);

require 'path/to/vendor/autoload.php';

use Innmind\BlackBox\Application;

Application::new([]) #(1)
    ->tryToProve(static function(): \Generator {
        // tests and proofs go here
    })
    ->exit();
```

1. You'll learn in a [later chapter](../organization/tags.md) what this array is. For now leave it like this.

The function passed to `tryToProve` must return a `Generator`. We'll see in the next chapters how to describe tests and proofs.

To execute your suite, run `php blackbox.php` in your terminal.
