<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer\Proof;

use Innmind\BlackBox\Runner\{
    Proof\Scenario\Failure,
    IO,
    Printer\Proof,
};
use Symfony\Component\VarDumper\{
    Dumper\CliDumper,
    Cloner\VarCloner,
};

final class Standard implements Proof
{
    private CliDumper $dumper;
    private VarCloner $cloner;
    private int $scenarii = 0;

    private function __construct()
    {
        $this->dumper = new CliDumper;
        $this->cloner = new VarCloner;
        $this->dumper->setColors(true);
    }

    public static function new(): self
    {
        return new self;
    }

    public function emptySet(IO $output, IO $error): void
    {
        $error("No scenario found\n");
    }

    public function success(IO $output, IO $error): void
    {
        $this->newLine($output);
        $output('.');
    }

    public function shrunk(IO $output, IO $error): void
    {
        $this->newLine($output);
        $output('S');
    }

    public function failed(IO $output, IO $error, Failure $failure): void
    {
        $this->newLine($output);
        $error("F\n");
        $output('$proof = ');
        $output(
            $this->dumper->dump(
                $this->cloner->cloneVar(
                    $failure->scenario()->unwrap(),
                ),
                true,
            ) ?? '',
        );

        /**
         * @var list<array{
         *      file?: string,
         *      line?: int,
         * }> $frame
         */
        $trace = $failure->assertion()->getTrace();
        $output("\n");

        foreach ($trace as $frame) {
            if (!\array_key_exists('file', $frame)) {
                continue;
            }

            if (!\array_key_exists('line', $frame)) {
                continue;
            }

            // do not render the internal calls to the runner, this path is the
            // one either locally on my machine or as a project dependency via
            // composer
            if (
                \str_contains($frame['file'], 'innmind/black-box/src/Runner') ||
                \str_contains($frame['file'], 'innmind/black-box/src/Application.php')
            ) {
                continue;
            }

            // same goal as above but for the GitHub Actions
            if (
                \str_contains($frame['file'], '/home/runner/work/BlackBox/BlackBox/src/Runner') ||
                \str_contains($frame['file'], '/home/runner/work/BlackBox/BlackBox/src/Application.php')
            ) {
                continue;
            }

            $output(\sprintf(
                "%s:%s\n",
                $frame['file'],
                $frame['line'],
            ));
        }
    }

    public function end(IO $output, IO $error): void
    {
        $output("\n\n");
    }

    private function newLine(IO $output): void
    {
        if ($this->scenarii !== 0 && $this->scenarii % 50 === 0) {
            $output("\n");
        }

        ++$this->scenarii;
    }
}
