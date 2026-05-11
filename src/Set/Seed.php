<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @template-covariant T
 */
final class Seed
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(Implementation<T>): Set<T> $wrap
     * @param Value<T> $value
     */
    private function __construct(
        private \Closure $wrap,
        private Value $value,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     *
     * @param \Closure(Implementation<A>): Set<A> $wrap
     * @param Value<A> $value
     *
     * @return self<A>
     */
    public static function of(
        \Closure $wrap,
        Value $value,
    ): self {
        return new self($wrap, $value);
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        return $this->value->unwrap();
    }

    /**
     * @return Set<T>
     */
    public function toSet(): Set
    {
        return ($this->wrap)(Seeded::implementation($this->value));
    }
}
