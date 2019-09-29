<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\Set;

trait BlackBox
{
    protected function forAll(Set $first, Set ...$sets): Scenario
    {
        return new Scenario($first, ...$sets);
    }
}
