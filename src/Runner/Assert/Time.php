<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\Stats;

final class Time
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
    public function inLessThan(): Time\InLessThan
    {
        return Time\InLessThan::of($this->stats, $this->action);
    }

    #[\NoDiscard]
    public function inMoreThan(): Time\InMoreThan
    {
        return Time\InMoreThan::of($this->stats, $this->action);
    }
}
