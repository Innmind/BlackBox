<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Implementation,
};

/**
 * @template T
 * @implements Provider<T>
 */
final class Composite implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<T>): Set<T> $wrap
     * @param callable(...mixed): T $aggregate
     * @param list<Implementation> $rest
     */
    private function __construct(
        private \Closure $wrap,
        private $aggregate,
        private Implementation $first,
        private Implementation $second,
        private array $rest,
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
     * @param callable(...mixed): A $aggregate
     *
     * @return self<A>
     */
    public static function of(
        \Closure $wrap,
        callable $aggregate,
        Implementation $first,
        Implementation $second,
        Implementation ...$rest,
    ): self {
        return new self($wrap, $aggregate, $first, $second, $rest, true);
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
            $this->aggregate,
            $this->first,
            $this->second,
            $this->rest,
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
            $this->aggregate,
            $this->first,
            $this->second,
            $this->rest,
            false,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function toSet(): Set
    {
        return ($this->wrap)(Set\Composite::implementation(
            $this->immutable,
            $this->aggregate,
            $this->first,
            $this->second,
            ...$this->rest,
        ));
    }
}
