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
        $output(
            $this->dumper->dump(
                $this->cloner->cloneVar(
                    $failure->scenario()->unwrap(),
                ),
                true,
            ) ?? '',
        );
        // TODO print the detail
    }

    public function end(IO $output, IO $error): void
    {
        $output("\n");
    }

    private function newLine(IO $output): void
    {
        if ($this->scenarii !== 0 && $this->scenarii % 50 === 0) {
            $output("\n");
        }

        ++$this->scenarii;
    }
}
