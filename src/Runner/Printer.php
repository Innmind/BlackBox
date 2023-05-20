<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

interface Printer
{
    public function start(): void;
    public function proof(Proof\Name $proof): Printer\Proof;
    public function end(): void;
}
