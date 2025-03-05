<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @template-covariant T
 */
final class Seed
{
    /**
     * @psalm-mutation-free
     *
     * @param Value<T> $value
     */
    private function __construct(
        private Value $value,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     *
     * @param Value<A> $value
     *
     * @return self<A>
     */
    public static function of(Value $value): self
    {
        return new self($value);
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        return $this->value->unwrap();
    }
}
