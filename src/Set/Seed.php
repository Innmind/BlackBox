<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @template T
 */
final class Seed
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(mixed): T $configure
     */
    private function __construct(
        private Value $value,
        private \Closure $configure,
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
        return new self($value, static fn($value): mixed => $value);
    }

    /**
     * @psalm-mutation-free
     * @template U
     *
     * @param callable(T): U $map
     *
     * @return self<U>
     */
    public function map(callable $map): self
    {
        $previous = $this->configure;

        return new self(
            $this->value,
            static fn($value) => $map($previous($value)),
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function shrinkable(): bool
    {
        return $this->value->shrinkable();
    }

    /**
     * @psalm-mutation-free
     *
     * @psalm-suppress InvalidNullableReturnType
     *
     * @return Dichotomy<T>
     */
    public function shrink(): Dichotomy
    {
        /** @psalm-suppress NullableReturnStatement */
        $shrunk = $this->value->shrink();

        /** @psalm-suppress ImpureMethodCall */
        $a = $shrunk->a();
        /** @psalm-suppress ImpureMethodCall */
        $b = $shrunk->b();
        $configure = $this->configure;

        // There's no need to define the immutability of the values here because
        // it's held by the values injected in the new Seeds.
        /** @psalm-suppress InvalidArgument Don't know why it complains on the Seed */
        return new Dichotomy(
            static fn() => Value::immutable(
                new Seed($a, $configure),
                // No dichotomy because the captured values in the configure
                // lambda is shrunk first
            ),
            static fn() => Value::immutable(
                new Seed($b, $configure),
                // No dichotomy because the captured values in the configure
                // lambda is shrunk first
            ),
        );
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        return ($this->configure)($this->value->unwrap());
    }
}
