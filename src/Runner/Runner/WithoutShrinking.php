<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Runner;

use Innmind\BlackBox\{
    Set\Value,
    Runner\Assert,
    Runner\Failure,
    Runner\Proof\Scenario,
    Runner\Printer,
};

final class WithoutShrinking
{
    /**
     * @param Value<Scenario> $scenario
     *
     * @throws Failure
     */
    public function __invoke(
        Printer\Proof $print,
        Assert $assert,
        Value $scenario,
    ): void {
        // TODO
    }
}
