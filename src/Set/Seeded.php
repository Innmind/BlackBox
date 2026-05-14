<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

/**
 * @internal
 * @template T
 * @implements Implementation<T>
 */
final class Seeded implements Implementation
{
    /**
     * @param Value<T> $value
     */
    private function __construct(
        private Value $value,
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
    ): \Generator {
        $value = $this->value->predicatedOn($predicate);

        if (!$value->acceptable()) {
            return;
        }

        while (true) {
            yield $value;
        }
    }

    /**
     * @internal
     *
     * @template A
     *
     * @param Value<A> $value
     *
     * @return self<A>
     */
    public static function implementation(Value $value): self
    {
        return new self($value);
    }
}
