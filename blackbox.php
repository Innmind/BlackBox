<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

use Innmind\BlackBox\{
    Application,
};

Application::new()
    ->tryToProve(function() {
        yield from (require __DIR__.'/proofs/add.php')();
    })
    ->exit();
