<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Runner\Printer,
    Runner\Proof,
    Runner\IO,
    Runner\Stats,
};

/**
 * @internal
 */
final class ExtractFailure implements Printer
{
    private \SplQueue $failures;

    public function __construct(\SplQueue $failures)
    {
        $this->failures = $failures;
    }

    public function start(IO $output, IO $error): void
    {
        // pass
    }

    public function proof(
        IO $output,
        IO $error,
        Proof\Name $proof,
        array $tags,
    ): Printer\Proof {
        return new ExtractFailure\OfProof($this->failures);
    }

    public function end(IO $output, IO $error, Stats $stats): void
    {
        // pass
    }
}
