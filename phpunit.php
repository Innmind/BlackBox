<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

use Innmind\BlackBox\{
    Application,
    PHPUnit\Load,
};

Application::new($argv)
    ->tryToProve(Load::directory(__DIR__.'/tests/'))
    ->exit();
