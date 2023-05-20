<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer;

use Innmind\BlackBox\Runner\{
    Proof\Scenario\Failure,
    IO,
};

interface Proof
{
    public function emptySet(IO $output, IO $error): void;
    public function success(IO $output, IO $error): void;
    public function shrunk(IO $output, IO $error): void;
    public function failed(IO $output, IO $error, Failure $failure): void;
    public function end(IO $output, IO $error): void;
}
