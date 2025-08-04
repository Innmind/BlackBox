<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\Stats;

final class Memory
{
    private Stats $stats;
    /** @var callable(): void */
    private $action;

    /**
     * @param callable(): void $action
     */
    private function __construct(Stats $stats, callable $action)
    {
        $this->stats = $stats;
        $this->action = $action;
    }

    /**
     * @internal
     *
     * @param callable(): void $action
     */
    public static function of(Stats $stats, callable $action): self
    {
        return new self($stats, $action);
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
