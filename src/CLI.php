<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Suites\Report,
    Suites\Report\Printer,
    Suites\Report\InMemory,
};
use Innmind\CLI\{
    Command,
    Command\Arguments,
    Command\Options,
    Environment,
};
use Innmind\Stream\Writable;
use Innmind\Url\PathInterface;
use Innmind\Server\Status\Server\Memory\Bytes;
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\{
    Period\Earth\Millisecond,
    ElapsedPeriodInterface,
};
use Innmind\Immutable\{
    StreamInterface,
    Str,
};
use Symfony\Component\VarDumper\{
    Cloner\VarCloner,
    Dumper\CliDumper,
};

final class CLI implements Command
{
    private $os;
    private $suites;
    private $paths;

    public function __construct(
        OperatingSystem $os,
        Suites $suites,
        PathInterface ...$paths
    ) {
        $this->os = $os;
        $this->suites = $suites;
        $this->paths = $paths;
    }

    public function __invoke(Environment $env, Arguments $arguments, Options $options): void
    {
        $report = new Printer(
            $env->output(),
            new InMemory
        );

        $start = $this->os->clock()->now();
        $report = ($this->suites)($this->os, $report, ...$this->paths);
        $end = $this->os->clock()->now();

        if ($report->failures()->size() > 0) {
            $env->exit(1);
        }

        $this->print($env->output(), $report->failures());
        $this->printResult($env->output(), $report, $end->elapsedSince($start));
    }

    public function __toString(): string
    {
        return <<<USAGE
test
USAGE;
    }

    private function print(Writable $stream, StreamInterface $failures): void
    {
        $cloner = new VarCloner;
        $dumper = new CliDumper;

        $dump = static function($value) use ($cloner, $dumper): Str {
            return Str::of($dumper->dump($cloner->cloneVar($value), true));
        };

        $failures->reduce(
            $stream,
            function(Writable $stream, Test\Report $report) use ($dump): Writable {
                return $stream
                    ->write(Str::of("\n\n"))
                    ->write(
                        Str::of((string) $report->name())->append(":\n\n")
                    )
                    ->write(Str::of("Given: "))
                    ->write($dump($report->failedScenario()))
                    ->write(Str::of("\n\n"))
                    ->write(Str::of("Result: "))
                    ->write($dump($report->failedResult()->value()))
                    ->write(Str::of("\n"))
                    ->write($report->failure()->message());
            }
        );
    }

    private function printResult(
        Writable $stream,
        Report $report,
        ElapsedPeriodInterface $duration
    ): void {
        $failures = $report->failures()->size();
        $result = 'OK';

        if ($failures > 0) {
            $result = 'KO';
        }

        $duration = new Millisecond($duration->milliseconds());
        $stream->write(
            Str::of("\n\nTime: %s m %s s %s ms, Memory: %s\n")->sprintf(
                $duration->minutes() + ($duration->hours() * 60),
                $duration->seconds(),
                $duration->milliseconds(),
                new Bytes(\memory_get_peak_usage())
            )
        );
        $stream->write(
            Str::of("\n%s (tests: %s, assertions: %s%s)\n")->sprintf(
                $result,
                \number_format($report->tests()),
                \number_format($report->assertions()),
                $failures > 0 ? ", failures: $failures" : ''
            )
        );
    }
}
