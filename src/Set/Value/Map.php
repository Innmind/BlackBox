<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Value;

/**
 * @internal
 * @template-covariant T
 */
final class Map
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(mixed): T $map
     */
    private function __construct(
        private \Closure $map,
    ) {
    }

    /**
     * @return T
     */
    public function __invoke(mixed $source): mixed
    {
        return ($this->map)($source);
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function noop(): self
    {
        return new self(static fn($source): mixed => $source);
    }

    /**
     * @psalm-mutation-free
     * @template V
     *
     * @param callable(T): V $map
     *
     * @return self<V>
     */
    public function with(callable $map): self
    {
        $previous = $this->map;

        return new self(
            static fn(mixed $source) => $map($previous($source)),
        );
    }
}
