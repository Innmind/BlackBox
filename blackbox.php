<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

use Innmind\BlackBox\{
    Application,
    Runner\Load,
};

Application::new($argv)
    ->tryToProve(Load::from(__DIR__.'/proofs/add.php'))
    ->exit();
