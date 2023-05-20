<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer\Proof;

use Innmind\BlackBox\Runner\{
    Proof\Scenario\Failure,
    IO,
    Printer\Proof,
};

final class Standard implements Proof
{
    private function __construct()
    {
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
        $output('.');
    }

    public function shrunk(IO $output, IO $error): void
    {
        $output('S');
    }

    public function failed(IO $output, IO $error, Failure $failure): void
    {
        $error("F\n");
        // TODO print the detail
    }

    public function end(IO $output, IO $error): void
    {
        $output("\n");
    }
}
