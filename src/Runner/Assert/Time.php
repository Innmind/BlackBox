<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\Stats;

final class Time
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

    public function inLessThan(): Time\InLessThan
    {
        return Time\InLessThan::of($this->stats, $this->action);
    }

    public function inMoreThan(): Time\InMoreThan
    {
        return Time\InMoreThan::of($this->stats, $this->action);
    }
}
