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
            if (!$scenario->shrinkable()) {
                throw Scenario\Failure::of($e, $scenario);
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
            $currentStrategy = $dichotomy->a();

            try {
                $currentStrategy->unwrap()($assert);
                $currentStrategy = $dichotomy->b();
                $currentStrategy->unwrap()($assert);
            } catch (Assert\Failure $e) {
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
                throw Scenario\Failure::of($e, $currentStrategy);
            }

            // when a and b work then the previous failure has been generated
            // with the smallest values possible
            throw Scenario\Failure::of($previousFailure, $previousStrategy);
            // we can use an infinite condition here since all exits are covered
        } while (true);
    }
}
