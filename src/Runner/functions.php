<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Collapse,
    Property,
    Properties,
};

/**
 * @param non-empty-string $name
 * @param callable(Assert, ...mixed): void $test
 */
function proof(
    string $name,
    Given $given,
    callable $test,
): Proof\Inline {
    return Proof\Inline::of(
        Proof\Name::of($name),
        $given,
        \Closure::fromCallable($test),
    );
}

/**
 * @param non-empty-string $name
 * @param callable(Assert): void $test
 */
function test(string $name, callable $test): Proof
{
    return Proof\Inline::test(
        Proof\Name::of($name),
        \Closure::fromCallable($test),
    );
}

/**
 * @no-named-arguments
 */
function given(Set|Provider $first, Set|Provider ...$rest): Given
{
    /** @var Set<list<mixed>> */
    $given = Collapse::of($first)->map(static fn(mixed $value) => [$value]);

    if (\count($rest) > 0) {
        /** @var Set<list<mixed>> */
        $given = Set::composite(
            static fn(mixed ...$args) => $args,
            $first,
            ...$rest,
        )
            ->immutable()
            ->toSet();
    }

    return Given::of(Set::randomize($given));
}

/**
 * @param class-string<Property> $property
 * @param Set<object>|Provider<object> $systemUnderTest
 */
function property(
    string $property,
    Set|Provider $systemUnderTest,
): Proof\Property {
    return Proof\Property::of($property, Collapse::of($systemUnderTest));
}

/**
 * @param non-empty-string $name
 * @param Set<Properties>|Provider<Properties> $properties
 * @param Set<object>|Provider<object> $systemUnderTest
 */
function properties(
    string $name,
    Set|Provider $properties,
    Set|Provider $systemUnderTest,
): Proof {
    return Proof\Properties::of(
        Proof\Name::of($name),
        Collapse::of($properties),
        Collapse::of($systemUnderTest),
    );
}
