<?php

require_once 'vendor/autoload.php';

use Innmind\BlackBox\{
    Runner,
    Runner\Printer\Simple,
    Random\RandomInt,
};

$runner = new Runner(100, true, new RandomInt, new Simple);

exit ((int) $runner(__DIR__.'/proofs/false.php'));
