<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Runner\Proof,
    Runner\Assert,
    Runner\Given,
    Property,
    Properties,
};

/**
 * @deprecated
 * @param non-empty-string $name
 * @param callable(Assert, ...mixed): void $test
 */
function proof(
    string $name,
    Given $given,
    callable $test,
): Proof\Inline {
    /** @psalm-suppress DeprecatedFunction */
    return \Innmind\BlackBox\Runner\proof($name, $given, $test);
}

/**
 * @deprecated
 * @param non-empty-string $name
 * @param callable(Assert): void $test
 */
function test(string $name, callable $test): Proof
{
    /** @psalm-suppress DeprecatedFunction */
    return \Innmind\BlackBox\Runner\test($name, $test);
}

/**
 * @deprecated
 * @no-named-arguments
 */
function given(Set|Provider $first, Set|Provider ...$rest): Given
{
    /** @psalm-suppress DeprecatedFunction */
    return \Innmind\BlackBox\Runner\given($first, ...$rest);
}

/**
 * @deprecated
 * @param class-string<Property> $property
 * @param Set<object>|Provider<object> $systemUnderTest
 */
function property(
    string $property,
    Set|Provider $systemUnderTest,
): Proof\Property {
    /** @psalm-suppress DeprecatedFunction */
    return \Innmind\BlackBox\Runner\property($property, $systemUnderTest);
}

/**
 * @deprecated
 * @param non-empty-string $name
 * @param Set<Properties>|Provider<Properties> $properties
 * @param Set<object>|Provider<object> $systemUnderTest
 */
function properties(
    string $name,
    Set|Provider $properties,
    Set|Provider $systemUnderTest,
): Proof {
    /** @psalm-suppress DeprecatedFunction */
    return \Innmind\BlackBox\Runner\properties($name, $properties, $systemUnderTest);
}
