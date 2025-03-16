<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Value;

use Innmind\BlackBox\Set\{
    Seed,
    Value,
};

/**
 * @internal
 * @template-covariant T
 */
final class Mutable
{
    private ?Seed $seed = null;

    /**
     * @psalm-mutation-free
     *
     * @param Map<T> $map
     */
    private function __construct(
        private mixed $source,
        private Map $map,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template V
     *
     * @param V|Seed<V> $value
     * @param Map<V> $map
     *
     * @return self<V>
     */
    public static function of($value, Map $map): self
    {
        return new self($value, $map);
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    public function mutable(bool $mutable): self
    {
        // Mutable values can't become immutable
        return $this;
    }

    /**
     * @psalm-mutation-free
     * @template V
     *
     * @param callable(T): (V|Seed<V>) $map
     *
     * @return self<V>
     */
    public function map(callable $map): self
    {
        return new self(
            $this->source,
            $this->map->with($map),
        );
    }

    public function seed(): ?Seed
    {
        return $this->seed;
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        $value = ($this->map)($this->source);

        // This is not ideal to hide the seeded value this way and to hijack
        // the shrinking system in self::shrinkable() and self::shrink() as it
        // complexifies the understanding of what's happening. Because now the
        // filtering can happen in 2 places.
        // Until a better idea comes along, this will stay this way.
        if ($value instanceof Seed) {
            $this->seed = $value;
            /** @var T */
            $value = $value->unwrap();
        }

        return $value;
    }
}
