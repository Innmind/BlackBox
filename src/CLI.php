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
    private $suites;
    private $paths;

    public function __construct(Suites $suites, PathInterface ...$paths)
    {
        $this->suites = $suites;
        $this->paths = $paths;
    }

    public function __invoke(Environment $env, Arguments $arguments, Options $options): void
    {
        $report = new Printer(
            $env->output(),
            new InMemory
        );

        $report = ($this->suites)($report, ...$this->paths);

        if ($report->failures()->size() > 0) {
            $env->exit(1);
        }

        $this->print($env->output(), $report->failures());
        $this->printResult($env->output(), $report);
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

    private function printResult(Writable $stream, Report $report): void
    {
        $failures = $report->failures()->size();
        $result = 'OK';

        if ($failures > 0) {
            $result = 'KO';
        }

        $stream->write(
            Str::of("\n\nMemory: %s\n")->sprintf(
                new Bytes(\memory_get_peak_usage())
            )
        );
        $stream->write(
            Str::of("%s (tests: %s, assertions: %s%s)\n")->sprintf(
                $result,
                \number_format($report->tests()),
                \number_format($report->assertions()),
                $failures > 0 ? ", failures: $failures" : ''
            )
        );
    }
}
