<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Runner;

use Innmind\BlackBox\{
    Set\Value,
    Runner\Assert,
    Runner\Assert\Debug,
    Runner\Proof\Scenario,
    Runner\Printer,
    Runner\IO,
};

/**
 * @internal
 */
final class WithoutShrinking
{
    /**
     * @param Value<Scenario> $scenario
     *
     * @throws Scenario\Failure
     */
    public function __invoke(
        Printer\Proof $print,
        IO $output,
        IO $error,
        Assert $assert,
        Value $scenario,
        Debug $debug,
    ): void {
        try {
            $scenario->unwrap()($assert);
            $print->success($output, $error);
        } catch (Assert\Failure $e) {
            throw Scenario\Failure::of($e, $scenario, $debug);
        }
    }
}
