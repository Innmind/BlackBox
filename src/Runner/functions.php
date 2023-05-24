<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Runner\Proof,
    Runner\Assert,
    Runner\Given,
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
): Proof {
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
function given(Set $first, Set ...$rest): Given
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

    return Given::of(Set\Randomize::of($given));
}

/**
 * @param class-string<Property> $property
 * @param Set<object> $systemUnderTest
 */
function property(
    string $property,
    Set $systemUnderTest,
): Proof\Property {
    return Proof\Property::of($property, $systemUnderTest);
}

/**
 * @param non-empty-string $name
 * @param Set<Properties> $properties
 * @param Set<object> $systemUnderTest
 */
function properties(
    string $name,
    Set $properties,
    Set $systemUnderTest,
): Proof {
    return Proof\Properties::of(
        Proof\Name::of($name),
        $properties,
        $systemUnderTest,
    );
}
