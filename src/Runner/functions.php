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

function all(Hold $hold, Hold ...$rest): Hold
{
    return Hold::all($hold, ...$rest);
}

function either(Hold $one, Hold $other): Hold
{
    return Hold::either($one, $other);
}

function exceptionThrown(): Hold
{
    return Hold::exceptionThrown();
}

function noExceptionThrown(): Hold
{
    return Hold::noExceptionThrown();
}

function exceptionCode(int $code): Hold
{
    return Hold::exceptionCode($code);
}

function exceptionMessage(string $message): Hold
{
    return Hold::exceptionMessage($message);
}

function isInstanceOf(string $class): Hold
{
    return Hold::instanceOf($class);
}

function isArray(): Hold
{
    return Hold::isArray();
}

function isBool(): Hold
{
    return Hold::isBool();
}

function isFloat(): Hold
{
    return Hold::isFloat();
}

function isInt(): Hold
{
    return Hold::isInt();
}

function isNumeric(): Hold
{
    return Hold::isNumeric();
}

function isObject(): Hold
{
    return Hold::isObject();
}

function isResource(): Hold
{
    return Hold::isResource();
}

function isString(): Hold
{
    return Hold::isString();
}

function isScalar(): Hold
{
    return Hold::isScalar();
}

function isCallable(): Hold
{
    return Hold::isCallable();
}

function isIterable(): Hold
{
    return Hold::isIterable();
}

function notInstanceOf(string $class): Hold
{
    return Hold::notInstanceOf($class);
}

function isNotArray(): Hold
{
    return Hold::isNotArray();
}

function isNotBool(): Hold
{
    return Hold::isNotBool();
}

function isNotFloat(): Hold
{
    return Hold::isNotFloat();
}

function isNotInt(): Hold
{
    return Hold::isNotInt();
}

function isNotNumeric(): Hold
{
    return Hold::isNotNumeric();
}

function isNotObject(): Hold
{
    return Hold::isNotObject();
}

function isNotResource(): Hold
{
    return Hold::isNotResource();
}

function isNotString(): Hold
{
    return Hold::isNotString();
}

function isNotScalar(): Hold
{
    return Hold::isNotScalar();
}

function isNotCallable(): Hold
{
    return Hold::isNotCallable();
}

function isNotIterable(): Hold
{
    return Hold::isNotIterable();
}

function arrayHasKey(string $key): Hold
{
    return Hold::arrayHasKey($key);
}

function arrayNotHasKey(string $key): Hold
{
    return Hold::arrayNotHasKey($key);
}

/**
 * @param mixed $value
 */
function inArray($value): Hold
{
    return Hold::inArray($value);
}

/**
 * @param mixed $value
 */
function notInArray($value): Hold
{
    return Hold::notInArray($value);
}

function size(int $count): Hold
{
    return Hold::count($count);
}

function notSize(int $count): Hold
{
    return Hold::notCount($count);
}

/**
 * @param int|float $value
 */
function greaterThan($value): Hold
{
    return Hold::greaterThan($value);
}

/**
 * @param int|float $value
 */
function greaterThanOrEqual($value): Hold
{
    return Hold::greaterThanOrEqual($value);
}

/**
 * @param int|float $value
 */
function lessThan($value): Hold
{
    return Hold::lessThan($value);
}

/**
 * @param int|float $value
 */
function lessThanOrEqual($value): Hold
{
    return Hold::lessThanOrEqual($value);
}

function stringStartsWith(string $start): Hold
{
    return Hold::stringStartsWith($start);
}

function stringDoesntStartWith(string $start): Hold
{
    return Hold::stringDoesntStartWith($start);
}

function stringContains(string $string): Hold
{
    return Hold::stringContains($string);
}

function stringDoesntContain(string $string): Hold
{
    return Hold::stringDoesntContain($string);
}

function stringEndsWith(string $end): Hold
{
    return Hold::stringEndsWith($end);
}

function stringDoesntEndWith(string $end): Hold
{
    return Hold::stringDoesntEndWith($end);
}

/**
 * @param callable(TestResult, ...mixed): bool $condition
 */
function satisfies(callable $condition, string $message = null): Hold
{
    return Hold::satisfies($condition, $message);
}

/**
 * @param callable(TestResult, ...mixed): bool $condition
 */
function doesntSatisfy(callable $condition, string $message = null): Hold
{
    return Hold::doesntSatisfy($condition, $message);
}

/**
 * @param callable(...mixed): mixed $find
 */
function same(callable $find, string $message = null): Hold
{
    return Hold::same($find, $message);
}

/**
 * @param callable(...mixed): mixed $find
 */
function notSame(callable $find, string $message = null): Hold
{
    return Hold::notSame($find, $message);
}

/**
 * @param mixed $value
 */
function is($value, string $message = null): Hold
{
    return Hold::is($value, $message);
}

/**
 * @param mixed $value
 */
function notIs($value, string $message = null): Hold
{
    return Hold::notIs($value, $message);
}

function matches(string $pattern): Hold
{
    return Hold::matches($pattern);
}

function doesntMatch(string $pattern): Hold
{
    return Hold::doesntMatch($pattern);
}
