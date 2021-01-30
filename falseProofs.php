<?php

require_once 'vendor/autoload.php';

use Innmind\BlackBox\BlackBox;

$code = BlackBox::of($argv)->tryToProve(
    (require __DIR__.'/proofs/false.php')(),
);

exit($code);
