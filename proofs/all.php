<?php
declare(strict_types = 1);

use Innmind\BlackBox\Runner\Load;

return static function(Load $load) {
    yield from $load(__DIR__.'/add.php');
    yield from $load(__DIR__.'/fixtures.php');
    yield from $load(__DIR__.'/runner/assert.php');
    yield from $load(__DIR__.'/runner/printer.php');
    yield from $load(__DIR__.'/application.php');
};
