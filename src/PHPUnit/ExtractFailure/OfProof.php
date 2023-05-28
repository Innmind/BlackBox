<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\ExtractFailure;

use Innmind\BlackBox\{
    Runner\Printer\Proof,
    Runner\Proof\Scenario,
    Runner\Proof\Scenario\Failure,
    Runner\IO,
    Runner\Assert\Failure\Property,
    Set\Value,
};

/**
 * @internal
 */
final class OfProof implements Proof
{
    /** @var \SplQueue<array{mixed, Value<Scenario>}> */
    private \SplQueue $failures;

    /**
     * @param \SplQueue<array{mixed, Value<Scenario>}> $failures
     */
    public function __construct(\SplQueue $failures)
    {
        $this->failures = $failures;
    }

    public function emptySet(IO $output, IO $error): void
    {
        // pass
    }

    public function success(IO $output, IO $error): void
    {
        // pass
    }

    public function shrunk(IO $output, IO $error): void
    {
        // pass
    }

    public function failed(IO $output, IO $error, Failure $failure): void
    {
        if (!($failure->assertion()->kind() instanceof Property)) {
            return;
        }

        $this->failures->enqueue([
            $failure->assertion()->kind()->value(),
            $failure->scenario(),
        ]);
    }

    public function end(IO $output, IO $error): void
    {
        // pass
    }
}
