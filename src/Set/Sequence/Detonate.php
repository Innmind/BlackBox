<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class Detonate
{
    /**
     * @template A
     *
     * @param list<Value<mixed>> $sequence
     *
     * @return list<mixed>
     */
    public function __invoke(array $sequence): array
    {
        return \array_map(
            static fn($value): mixed => $value->unwrap(),
            $sequence,
        );
    }
}
