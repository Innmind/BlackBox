<?php

require_once 'vendor/autoload.php';

use Innmind\BlackBox\BlackBox;

$code = BlackBox::of()->tryToProve(__DIR__.'/proofs/all.php');

exit($code);
