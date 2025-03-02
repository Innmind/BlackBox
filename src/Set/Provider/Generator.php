<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Implementation,
    Random,
};

/**
 * @template T
 * @implements Provider<T>
 */
final class Generator implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<T>): Set<T> $wrap
     * @param callable(Random): \Generator<T> $factory
     */
    private function __construct(
        private \Closure $wrap,
        private $factory,
        private bool $immutable,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     * @no-named-arguments
     *
     * @param pure-Closure(Implementation<A>): Set<A> $wrap
     * @param callable(Random): \Generator<A> $factory
     *
     * @return self<A>
     */
    public static function of(
        \Closure $wrap,
        callable $factory,
    ): self {
        return new self($wrap, $factory, true);
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    public function immutable(): self
    {
        return new self(
            $this->wrap,
            $this->factory,
            true,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    public function mutable(): self
    {
        return new self(
            $this->wrap,
            $this->factory,
            false,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function toSet(): Set
    {
        return ($this->wrap)(Set\FromGenerator::implementation(
            $this->factory,
            $this->immutable,
        ));
    }
}
