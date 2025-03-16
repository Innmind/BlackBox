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
final class Map
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(mixed): (T|Seed<T>) $map
     */
    private function __construct(
        private \Closure $map,
    ) {
    }

    /**
     * @return T|Seed<T>
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
     * @param callable(T): (V|Seed<V>) $map
     *
     * @return self<V>
     */
    public function with(callable $map): self
    {
        $previous = $this->map;

        return new self(static function(mixed $source) use ($map, $previous): mixed {
            $value = $previous($source);

            if ($value instanceof Seed) {
                return $value->flatMap(static function($value) use ($map) {
                    /** @var T $value */
                    $mapped = $map($value);

                    if ($mapped instanceof Seed) {
                        return $mapped;
                    }

                    return Seed::of(Value::of($mapped));
                });
            }

            return $map($value);
        });
    }
}
