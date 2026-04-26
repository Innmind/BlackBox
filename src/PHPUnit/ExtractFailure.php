<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\Runner\{
    Printer,
    Proof,
    IO,
    Stats,
};

/**
 * @internal
 */
final class ExtractFailure implements Printer
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
    public function start(IO $output, IO $error): void
    {
        // pass
    }

    #[\Override]
    public function proof(
        IO $output,
        IO $error,
        Proof\Name $proof,
        array $tags,
    ): Printer\Proof {
        return new ExtractFailure\OfProof($this->failures);
    }

    #[\Override]
    public function end(IO $output, IO $error, Stats $stats): void
    {
        // pass
    }
}
