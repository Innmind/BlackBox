<?php

require_once 'vendor/autoload.php';

use Innmind\BlackBox\BlackBox;

$code = BlackBox::of($argv)->tryToProve(__DIR__.'/proofs/false.php');

exit($code);
