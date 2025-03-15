<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Runner;

use Innmind\BlackBox\{
    Set\Value,
    Runner\Assert,
    Runner\Proof\Scenario,
    Runner\Printer,
    Runner\IO,
};

/**
 * @internal
 */
final class WithShrinking
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
    ): void {
        try {
            $scenario->unwrap()($assert);
            $print->success($output, $error);
        } catch (Assert\Failure $e) {
            $this->shrink(
                $e,
                $print,
                $output,
                $error,
                $assert,
                $scenario,
            );
        }
    }

    /**
     * @param Value<Scenario> $scenario
     *
     * @throws Scenario\Failure
     */
    private function shrink(
        Assert\Failure $previousFailure,
        Printer\Proof $print,
        IO $output,
        IO $error,
        Assert $assert,
        Value $scenario,
    ): void {
        $previousStrategy = $scenario;
        $dichotomy = $scenario->shrink();

        do {
            if (\is_null($dichotomy)) {
                throw Scenario\Failure::of($previousFailure, $previousStrategy);
            }

            $currentStrategy = $dichotomy->a();

            try {
                $currentStrategy->unwrap()($assert);
                $currentStrategy = $dichotomy->b();
                $currentStrategy->unwrap()($assert);

                // When a and b work then we assign this variable to null so at
                // the next loop iteration it'll throw the exception above
                $dichotomy = null;
            } catch (Assert\Failure $e) {
                $dichotomy = $currentStrategy->shrink();
                $previousFailure = $e;
                $previousStrategy = $currentStrategy;

                $print->shrunk($output, $error);

                continue;
            }
            // we can use an infinite condition here since all exits are covered
        } while (true);
    }
}
