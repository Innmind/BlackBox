<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\Set;

trait BlackBox
{
    protected function forAll(Set $first, Set ...$sets): Scenario
    {
        $scenario = new Scenario($first, ...$sets);
        $size = \getenv('BLACKBOX_SET_SIZE');

        if ($size !== false) {
            $scenario = $scenario->take((int) $size);
        }

        return $scenario;
    }
}
