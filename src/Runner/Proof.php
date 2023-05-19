<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Runner\Proof\Name;

interface Proof
{
    public function name(): Name;
    public function test(Assert $assert): mixed;
}
