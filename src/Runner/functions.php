<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Runner\Proof,
    Runner\Given,
    Runner\When,
    Runner\Then,
    Runner\Hold,
    Runner\TestResult,
    Set,
};

function proof(string $name, Given $given, When $when, Then $then): Proof
{
    return new Proof($name, $given, $when, $then);
}

function given(Set $first, Set ...$rest): Given
{
    return new Given($first, ...$rest);
}

/**
 * @param callable(...mixed): mixed $test
 */
function when(callable $test): When
{
    return new When($test);
}

function then(Hold $hold, Hold ...$rest): Then
{
    return new Then($hold, ...$rest);
}

/**
 * @param callable(callable(): void, callable(string): void, TestResult, ...mixed): void $assertion
 */
function hold(callable $assertion): Hold
{
    return new Hold($assertion);
}
