<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

interface Printer
{
    public function start(IO $output, IO $error): void;

    /**
     * @param list<\UnitEnum> $tags
     */
    #[\NoDiscard]
    public function proof(
        IO $output,
        IO $error,
        Proof\Name $proof,
        array $tags,
    ): Printer\Proof;
    public function end(IO $output, IO $error, Stats $stats): void;
}
