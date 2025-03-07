<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Util\Slice as Util,
};

/**
 * @implements Provider<Util>
 */
final class Slice implements Provider
{
    /** @var 0|positive-int */
    private int $min;
    /** @var 0|positive-int */
    private int $max;
    /** @var 0|positive-int */
    private int $atLeast;

    /**
     * @psalm-mutation-free
     *
     * @param 0|positive-int $min
     * @param 0|positive-int $max
     * @param 0|positive-int $atLeast
     */
    private function __construct(int $min, int $max, int $atLeast)
    {
        $this->min = $min;
        $this->max = $max;
        $this->atLeast = $atLeast;
    }

    /**
     * @psalm-pure
     */
    public static function any(): self
    {
        return new self(0, 100, 0);
    }

    /**
     * @psalm-pure
     *
     * @param 0|positive-int $min
     * @param 0|positive-int $max
     */
    public static function between(int $min, int $max): self
    {
        return new self($min, $max, 0);
    }

    /**
     * @psalm-mutation-free
     *
     * @param 0|positive-int $length
     */
    public function atLeast(int $length): self
    {
        return new self(
            $this->min,
            $this->max,
            $length,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     *
     * @return Set<Util>
     */
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(Util): bool $predicate
     *
     * @return Set<Util>
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
     * @param callable(Util): (V|Set\Seed<V>) $map
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
     * @param callable(Seed<Util>): (Set<V>|Provider<V>) $map
     *
     * @return Set<V>
     */
    public function flatMap(callable $map): Set
    {
        return $this->toSet()->flatMap($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<Util>
     */
    #[\Override]
    public function toSet(): Set
    {
        return Set::compose(
            Util::of(...),
            Set::integers()->between($this->min, $this->max),
            Set::integers()->between($this->atLeast, $this->max - $this->min),
            Set::of($this->atLeast),
            Set::of(true, false),
        )
            ->immutable()
            ->toSet();
    }
}
