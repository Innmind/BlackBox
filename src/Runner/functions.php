<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Runner\Proof,
    Runner\Assert,
};

/**
 * @param non-empty-string $name
 * @param Set<list<mixed>> $given
 * @param callable(Assert, ...mixed): void $test
 */
function proof(
    string $name,
    Set $given,
    callable $test,
): Proof {
    return Proof\Generic::of(
        Proof\Name::of($name),
        $given,
        \Closure::fromCallable($test),
    );
}

/**
 * @no-named-arguments
 *
 * @return Set<list<mixed>>
 */
function given(Set $first, Set ...$rest): Set
{
    /** @var Set<list<mixed>> */
    $given = Set\Decorate::immutable(
        static fn(mixed $value) => [$value],
        $first,
    );

    if (\count($rest) > 0) {
        /** @var Set<list<mixed>> */
        $given = Set\Composite::immutable(
            static fn(mixed ...$args) => $args,
            $first,
            ...$rest,
        );
    }

    return Set\Randomize::of($given);
}
