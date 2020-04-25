<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

interface Property
{
    public function name(): string;
    public function applicableTo(object $systemUnderTest): bool;

    /**
     * @throws \Exception Any exception understood by your test framework
     *
     * @return object The system under test with the property applied
     */
    public function ensureHeldBy(object $systemUnderTest): object;
}
