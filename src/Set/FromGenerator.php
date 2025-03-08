<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};

/**
 * @internal
 * @template T
 * @implements Implementation<T>
 */
final class FromGenerator implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(Random): \Generator<T> $generatorFactory
     * @param \Closure(T): bool $predicate
     * @param int<1, max> $size
     */
    private function __construct(
        private \Closure $generatorFactory,
        private \Closure $predicate,
        private int $size,
        private bool $immutable,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template V
     *
     * @param callable(Random): \Generator<V> $generatorFactory
     *
     * @return self<V>
     */
    public static function implementation(
        callable $generatorFactory,
        bool $immutable,
    ): self {
        return new self(
            \Closure::fromCallable($generatorFactory),
            static fn(): bool => true,
            100,
            $immutable,
        );
    }

    /**
     * @deprecated Use Set::generator()->immutable() instead
     * @template V
     *
     * @param callable(Random): \Generator<V> $generatorFactory
     *
     * @return Set<V>
     */
    public static function of(callable $generatorFactory): Set
    {
        return Set::generator(self::guard($generatorFactory))
            ->immutable()
            ->toSet();
    }

    /**
     * @deprecated Use Set::generator()->mutable() instead
     * @template V
     *
     * @param callable(Random): \Generator<V> $generatorFactory
     *
     * @return Set<V>
     */
    public static function mutable(callable $generatorFactory): Set
    {
        return Set::generator(self::guard($generatorFactory))
            ->mutable()
            ->toSet();
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->generatorFactory,
            $this->predicate,
            $size,
            $this->immutable,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): self
    {
        $previous = $this->predicate;

        return new self(
            $this->generatorFactory,
            static function(mixed $value) use ($previous, $predicate): bool {
                /** @var T */
                $value = $value;

                if (!$previous($value)) {
                    return false;
                }

                return $predicate($value);
            },
            $this->size,
            $this->immutable,
        );
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        $generator = ($this->generatorFactory)($random);
        $iterations = 0;

        while ($iterations < $this->size && $generator->valid()) {
            /** @var T */
            $value = $generator->current();

            if (($this->predicate)($value)) {
                yield match ($this->immutable) {
                    true => Value::immutable($value),
                    false => Value::mutable(static fn() => $value),
                };

                ++$iterations;
            }

            $generator->next();
        }

        if ($iterations === 0) {
            throw new EmptySet;
        }
    }

    /**
     * @template A
     *
     * @param callable(Random): \Generator<A> $generatorFactory
     *
     * @return callable(Random): \Generator<A>
     */
    private static function guard(callable $generatorFactory): callable
    {
        if (!$generatorFactory(Random::mersenneTwister) instanceof \Generator) {
            throw new \TypeError('Argument 1 must be of type callable(): \Generator');
        }

        return $generatorFactory;
    }
}
