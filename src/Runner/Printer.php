<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use SebastianBergmann\Timer\{
    Timer,
    ResourceUsageFormatter,
};

final class Printer
{
    private function __construct(
        private Timer $timer,
        private Printer\Proof $proof,
        private bool $addGroups,
    ) {
    }

    /**
     * @internal
     */
    public static function new(): self
    {
        $addGroups = \getenv('GITHUB_ACTIONS') === 'true';

        return new self(
            new Timer,
            Printer\Proof::new(
                \getenv('LC_TERMINAL') === 'iTerm2',
                $addGroups,
            ),
            $addGroups,
        );
    }

    public function withoutColors(): self
    {
        return new self(
            $this->timer,
            $this->proof->withoutColors(),
            $this->addGroups,
        );
    }

    public function disableGitHubOutput(): self
    {
        return new self(
            $this->timer,
            $this->proof->disableGitHubOutput(),
            false,
        );
    }

    public function start(IO $output, IO $error): void
    {
        $this->timer->start();

        $output("BlackBox\n");
    }

    /**
     * @param list<\UnitEnum> $tags
     */
    #[\NoDiscard]
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

        return $this->proof;
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
