<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Runner\Printer,
    Runner\Proof,
    Runner\Proof\Scenario,
    Runner\IO,
    Runner\Stats,
    Set\Value,
};

/**
 * @internal
 */
final class ExtractFailure implements Printer
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
