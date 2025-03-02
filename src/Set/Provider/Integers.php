<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Implementation,
};

/**
 * @implements Provider<int>
 */
final class Integers implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<int>): Set<int> $wrap
     */
    private function __construct(
        private \Closure $wrap,
        private ?int $min,
        private ?int $max,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @param pure-Closure(Implementation<int>): Set<int> $wrap
     */
    public static function of(\Closure $wrap): self
    {
        return new self($wrap, null, null);
    }

    /**
     * @psalm-mutation-free
     */
    public function between(int $min, int $max): self
    {
        return new self($this->wrap, $min, $max);
    }

    /**
     * @psalm-mutation-free
     */
    public function above(int $min): self
    {
        return new self($this->wrap, $min, null);
    }

    /**
     * @psalm-mutation-free
     */
    public function below(int $max): self
    {
        return new self($this->wrap, null, $max);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<int>
     */
    public function exceptZero(): Set
    {
        return $this->filter(
            static fn(int $value): bool => $value !== 0,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<int<0, max>>
     */
    public function naturalNumbers(): Set
    {
        /** @var Set<0|positive-int> */
        return $this
            ->above(0)
            ->toSet();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<int<1, max>>
     */
    public function naturalNumbersExceptZero(): Set
    {
        /** @var Set<int<1, max>> */
        return $this
            ->above(1)
            ->toSet();
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return Set<int>
     */
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(int): bool $predicate
     *
     * @return Set<int>
     */
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(int): V $map
     *
     * @return Set<V>
     */
    public function map(callable $map): Set
    {
        return $this->toSet()->map($map);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function toSet(): Set
    {
        return ($this->wrap)(Set\Integers::implementation(
            $this->min,
            $this->max,
        ));
    }
}
