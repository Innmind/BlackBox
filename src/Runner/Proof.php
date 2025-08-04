<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Runner\Proof\Name,
    Runner\Proof\Scenario,
    Set,
};

interface Proof
{
    public function name(): Name;

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    #[\NoDiscard]
    public function tag(\UnitEnum ...$tags): self;

    /**
     * @return list<\UnitEnum>
     */
    public function tags(): array;

    /**
     * @param positive-int $count
     *
     * @return Set<Scenario>
     */
    public function scenarii(int $count): Set;
}
