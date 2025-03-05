<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Seed,
    Set\Implementation,
};

/**
 * @implements Provider<float>
 */
final class RealNumbers implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<float>): Set<float> $wrap
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
     * @param pure-Closure(Implementation<float>): Set<float> $wrap
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
     * @param int<1, max> $size
     *
     * @return Set<float>
     */
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(float): bool $predicate
     *
     * @return Set<float>
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
     * @param callable(float): V $map
     *
     * @return Set<V>
     */
    public function map(callable $map): Set
    {
        return $this->toSet()->map($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(Seed<float>): (Set<V>|Provider<V>) $map
     *
     * @return Set<V>
     */
    public function flatMap(callable $map): Set
    {
        return $this->toSet()->flatMap($map);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function toSet(): Set
    {
        return ($this->wrap)(Set\RealNumbers::implementation(
            $this->min,
            $this->max,
        ));
    }
}
