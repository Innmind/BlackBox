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
    private bool $withColors;
    private bool $addMarks;
    private bool $addGroups;

    private function __construct(
        bool $withColors,
        ?Timer $timer = null,
        ?bool $addMarks = null,
        ?bool $addGroups = null,
    ) {
        $this->timer = $timer ?? new Timer;
        $this->withColors = $withColors;
        $this->addMarks = $addMarks ?? \getenv('LC_TERMINAL') === 'iTerm2';
        $this->addGroups = $addGroups ?? \getenv('GITHUB_ACTIONS') === 'true';
    }

    /**
     * @internal
     */
    public static function new(): self
    {
        return new self(true);
    }

    public static function withoutColors(): self
    {
        return new self(false);
    }

    public function disableGitHubGrouping(): self
    {
        return new self(
            $this->withColors,
            $this->timer,
            $this->addMarks,
            false,
        );
    }

    public function start(IO $output, IO $error): void
    {
        $this->timer->start();

        $output("BlackBox\n");
    }

    public function proof(
        IO $output,
        IO $error,
        Proof\Name $proof,
        array $tags,
    ): Printer\Proof {
        $header = '';

        foreach ($tags as $tag) {
            $header .= "[{$tag->name}]";
        }

        if (\count($tags) > 0) {
            $header .= ' ';
        }

        $header .= $proof->toString().":\n";

        if ($this->addGroups) {
            $header = '::group::'.$header;
        }

        $output($header);

        return Printer\Proof\Standard::new(
            $proof->toString(),
            $this->withColors,
            $this->addMarks,
            $this->addGroups,
        );
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
            false => $output("Failed\n$statsToPrint, Failures: {$stats->failures()}\n"),
        };
    }
}
