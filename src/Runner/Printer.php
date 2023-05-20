<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

interface Printer
{
    public function start(IO $output, IO $error): void;
    public function proof(IO $output, IO $error, Proof\Name $proof): Printer\Proof;
    public function end(IO $output, IO $error): void;
}
