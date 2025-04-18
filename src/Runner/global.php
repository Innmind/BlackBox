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
 * @param non-empty-string $name
 * @param callable(Assert, ...mixed): void $test
 */
function proof(
    string $name,
    Given $given,
    callable $test,
): Proof\Inline {
    return \Innmind\BlackBox\Runner\proof($name, $given, $test);
}

/**
 * @param non-empty-string $name
 * @param callable(Assert): void $test
 */
function test(string $name, callable $test): Proof
{
    return \Innmind\BlackBox\Runner\test($name, $test);
}

/**
 * @no-named-arguments
 */
function given(Set|Provider $first, Set|Provider ...$rest): Given
{
    return \Innmind\BlackBox\Runner\given($first, ...$rest);
}

/**
 * @param class-string<Property> $property
 * @param Set<object>|Provider<object> $systemUnderTest
 */
function property(
    string $property,
    Set|Provider $systemUnderTest,
): Proof\Property {
    return \Innmind\BlackBox\Runner\property($property, $systemUnderTest);
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
    return \Innmind\BlackBox\Runner\properties($name, $properties, $systemUnderTest);
}
