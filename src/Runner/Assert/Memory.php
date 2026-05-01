<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\Stats;

final class Memory
{
    /**
     * @param \Closure(): void $action
     */
    private function __construct(
        private Stats $stats,
        private \Closure $action,
    ) {
    }

    /**
     * @internal
     *
     * @param callable(): void $action
     */
    public static function of(Stats $stats, callable $action): self
    {
        return new self($stats, \Closure::fromCallable($action));
    }

    #[\NoDiscard]
    public function inLessThan(): Memory\InLessThan
    {
        return Memory\InLessThan::of($this->stats, $this->action);
    }

    #[\NoDiscard]
    public function inMoreThan(): Memory\InMoreThan
    {
        return Memory\InMoreThan::of($this->stats, $this->action);
    }
}
