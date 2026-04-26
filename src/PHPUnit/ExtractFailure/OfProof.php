<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\ExtractFailure;

use Innmind\BlackBox\Runner\{
    Printer\Proof,
    Proof\Scenario\Failure,
    IO,
    Assert\Failure\Property,
};

/**
 * @internal
 */
final class OfProof implements Proof
{
    /** @var \SplQueue<array{mixed, list<array{string, mixed}>}> */
    private \SplQueue $failures;

    /**
     * @param \SplQueue<array{mixed, list<array{string, mixed}>}> $failures
     */
    public function __construct(\SplQueue $failures)
    {
        $this->failures = $failures;
    }

    #[\Override]
    public function emptySet(IO $output, IO $error): void
    {
        // pass
    }

    #[\Override]
    public function success(IO $output, IO $error): void
    {
        // pass
    }

    #[\Override]
    public function shrunk(IO $output, IO $error): void
    {
        // pass
    }

    #[\Override]
    public function failed(
        IO $output,
        IO $error,
        Failure $failure,
    ): void {
        if (!($failure->assertion()->kind() instanceof Property)) {
            return;
        }

        $this->failures->enqueue([
            $failure->assertion()->kind()->value(),
            $failure->parameters(),
        ]);
    }

    #[\Override]
    public function end(IO $output, IO $error): void
    {
        // pass
    }
}
