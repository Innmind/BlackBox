<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Runner;

use Innmind\BlackBox\{
    Set\Value,
    Runner\Assert,
    Runner\Failure,
    Runner\Proof\Scenario,
};

final class WithoutShrinking
{
    /**
     * @param Value<Scenario> $scenario
     *
     * @throws Failure
     */
    public function __invoke(Assert $assert, Value $scenario): void
    {
        // TODO
    }
}
