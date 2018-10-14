<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assert;

use Innmind\BlackBox\Assertion;
use Innmind\Immutable\SequenceInterface;

function same($value): Assertion
{
    return new Assertion\Same($value);
}

function notSame($value): Assertion
{
    return new Assertion\NotSame($value);
}

function true(): Assertion
{
    return same(true);
}

function false(): Assertion
{
    return same(false);
}

function contains($value): Assertion
{
    return new Assertion\Contains($value);
}

function notContains($value): Assertion
{
    return new Assertion\NotContains($value);
}

function count(int $count): Assertion
{
    return new Assertion\Count($count);
}

function notCount(int $count): Assertion
{
    return new Assertion\NotCount($count);
}

function instance(string $class): Assertion
{
    return new Assertion\Instance($class);
}

function primitive(string $type): Assertion
{
    return new Assertion\Primitive($type);
}

function int(): Assertion
{
    return primitive('int');
}

function float(): Assertion
{
    return primitive('float');
}

function object(): Assertion
{
    return primitive('object');
}

function null(): Assertion
{
    return primitive('null');
}

function bool(): Assertion
{
    return primitive('bool');
}

function string(): Assertion
{
    return primitive('string');
}

function iterable(): Assertion
{
    return primitive('iterable');
}

function resource(): Assertion
{
    return primitive('resource');
}

function fn(): Assertion
{
    return primitive('callable');
}

function regex(string $pattern): Assertion
{
    return new Assertion\Regex($pattern);
}

function sequence(): Assertion
{
    return instance(SequenceInterface::class);
}

function stream(string $type): Assertion
{
    return new Assertion\Stream($type);
}

function set(string $type): Assertion
{
    return new Assertion\Set($type);
}

function map(string $key, string $value): Assertion
{
    return new Assertion\Map($key, $value);
}

function that(callable $predicate): Assertion
{
    return new Assertion\That($predicate);
}

function exception(string $class, string $message = null, int $code = null): Assertion
{
    return new Assertion\Exception($class, $message, $code);
}
