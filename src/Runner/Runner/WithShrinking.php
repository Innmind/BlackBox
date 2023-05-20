<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Runner;

use Innmind\BlackBox\{
    Set\Value,
    Runner\Assert,
    Runner\Failure,
    Runner\Proof\Scenario,
    Runner\Printer,
    Runner\IO,
};

final class WithShrinking
{
    /**
     * @param Value<Scenario> $scenario
     *
     * @throws Failure
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
        } catch (Failure $e) {
            if (!$scenario->shrinkable()) {
                throw $e;
            }

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
     * @throws Failure
     */
    private function shrink(
        Failure $previousFailure,
        Printer\Proof $print,
        IO $output,
        IO $error,
        Assert $assert,
        Value $scenario,
    ): void {
        $previousStrategy = $scenario;
        $dichotomy = $scenario->shrink();

        do {
            $currentStrategy = $dichotomy->a();

            try {
                $currentStrategy->unwrap()($assert);
                $currentStrategy = $dichotomy->b();
                $currentStrategy->unwrap()($assert);
            } catch (Failure $e) {
                if ($currentStrategy->shrinkable()) {
                    $dichotomy = $currentStrategy->shrink();
                    $previousFailure = $e;
                    $previousStrategy = $currentStrategy;

                    $print->shrunk($output, $error);

                    continue;
                }

                // current strategy no longer shrinkable so it means we reached
                // a leaf of our search tree meaning the current exception is the
                // last one we can obtain
                throw $e;
            }

            // when a and b work then the previous failure has been generated
            // with the smallest values possible
            throw $previousFailure;
            // we can use an infinite condition here since all exits are covered
        } while (true);
    }
}
