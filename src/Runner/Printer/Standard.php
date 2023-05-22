<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer;

use Innmind\BlackBox\Runner\{
    Printer,
    IO,
    Proof,
    Stats,
};
use SebastianBergmann\Timer\{
    Timer,
    ResourceUsageFormatter,
};

final class Standard implements Printer
{
    private Timer $timer;

    private function __construct()
    {
        $this->timer = new Timer;
    }

    public static function new(): self
    {
        return new self;
    }

    public function start(IO $output, IO $error): void
    {
        $this->timer->start();

        $output("BlackBox\n");
    }

    public function proof(IO $output, IO $error, Proof\Name $proof): Printer\Proof
    {
        $output($proof->toString().":\n");

        return Printer\Proof\Standard::new();
    }

    public function end(IO $output, IO $error, Stats $stats): void
    {
        $statsToPrint = \sprintf(
            'Proofs: %s, Scenarii: %s, Assertions: %s',
            $stats->proofs(),
            $stats->scenarii(),
            $stats->assertions(),
        );

        $output((new ResourceUsageFormatter)->resourceUsage($this->timer->stop()));
        $output("\n\n");

        match ($stats->successful()) {
            true => $output("OK\n$statsToPrint\n"),
            false => $error("Failed\n$statsToPrint, Failures: {$stats->failures()}\n"),
        };
    }
}
