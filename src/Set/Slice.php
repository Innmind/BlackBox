<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Util\Slice as Util,
};

/**
 * @implements Set<Util>
 */
final class Slice implements Set
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
     */
    #[\Override]
    public function take(int $size): Set
    {
        return $this->collapse()->take($size);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): Set
    {
        return $this->collapse()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function map(callable $map): Set
    {
        return $this->collapse()->map($map);
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        yield from $this->collapse()->values($random);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<Util>
     */
    private function collapse(): Set
    {
        return Composite::immutable(
            Util::of(...),
            Integers::between($this->min, $this->max),
            Integers::between($this->atLeast, $this->max - $this->min),
            Elements::of($this->atLeast),
            Elements::of(true, false),
        );
    }
}
