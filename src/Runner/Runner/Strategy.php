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
enum Strategy
{
    case keepErrorType;
    case exhaustive;

    /**
     * @param Value<Scenario> $scenario
     *
     * @throws Scenario\Failure
     */
    public function __invoke(
        Printer\Proof $print,
        IO $output,
        Assert $assert,
        Value $scenario,
        Debug $debug,
    ): void {
        try {
            $scenario->unwrap()($assert);
            $print->success($output);
        } catch (Assert\Failure $e) {
            $this->shrink(
                $e,
                $print,
                $output,
                $assert,
                $scenario,
                $debug,
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
        Assert $assert,
        Value $scenario,
        Debug $debug,
    ): void {
        $identity = $this->hash($previousFailure);
        $previousStrategy = $scenario;
        $dichotomy = $scenario->shrink();

        do {
            if (\is_null($dichotomy)) {
                throw Scenario\Failure::of(
                    $previousFailure,
                    $previousStrategy,
                    $debug,
                );
            }

            $currentStrategy = $dichotomy->a();

            try {
                try {
                    $debug->reset();
                    $currentStrategy->unwrap()($assert);
                } catch (Assert\Failure $e) {
                    if ($this->canShrink($e, $identity)) {
                        // throwing here will go call the shrink method in the
                        // catch below
                        throw $e;
                    }

                    // if it can't shrink due to error identity change then we
                    // still try the B strategy to have a more fine grained
                    // shrunk value
                }
                $currentStrategy = $dichotomy->b();

                try {
                    $debug->reset();
                    $currentStrategy->unwrap()($assert);
                } catch (Assert\Failure $e) {
                    if ($this->canShrink($e, $identity)) {
                        // throwing here will go call the shrink method in the
                        // catch below
                        throw $e;
                    }

                    // if the error identity changes with the B strategy then it
                    // means we reached the most fine grained shrunk value that
                    // still correspond to the initial error.
                    // Here we do nothing meaning we fall through the case below
                    // where we assign $dichotomy to null to stop the shrinking.
                }

                // When a and b work then we assign this variable to null so at
                // the next loop iteration it'll throw the exception above
                $dichotomy = null;
            } catch (Assert\Failure $e) {
                $dichotomy = $currentStrategy->shrink();
                $previousFailure = $e;
                $previousStrategy = $currentStrategy;

                $print->shrunk($output);

                continue;
            }
            // we can use an infinite condition here since all exits are covered
        } while (true);
    }

    private function canShrink(Assert\Failure $e, string $identity): bool
    {
        if ($this === self::exhaustive) {
            return true;
        }

        return $this->hash($e) === $identity;
    }

    private function hash(Assert\Failure $e): string
    {
        $trace = $e->getTrace();
        /** @var array{file: string, line: string} */
        $kind = $trace[0];
        /** @var array{file: string, line: string} */
        $userCall = $trace[1];

        return \hash(
            'xxh3',
            $kind['file'].$kind['line'].$userCall['file'].$userCall['line'],
        );
    }
}
