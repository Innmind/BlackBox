<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Exception\LogicException;
use Innmind\Json\Json;

function integers(int $range = 100): \Generator
{
    if ($range < 1) {
        throw new LogicException;
    }

    for ($i = 0; $i < $range; $i++) {
        yield \random_int(\PHP_INT_MIN, \PHP_INT_MAX);
    }
}

function integersExceptZero(int $range = 100): \Generator
{
    if ($range < 1) {
        throw new LogicException;
    }

    for ($i = 0; $i < $range; $i++) {
        $int = \random_int(\PHP_INT_MIN, \PHP_INT_MAX);

        if ($int === 0) {
            continue;
        }

        yield $int;
    }
}

function naturalNumbers(int $range = 100): \Generator
{
    if ($range < 1) {
        throw new LogicException;
    }

    for ($i = 0; $i < $range; $i++) {
        yield \random_int(0, \PHP_INT_MAX);
    }
}

function naturalNumbersExceptZero(int $range = 100): \Generator
{
    if ($range < 1) {
        throw new LogicException;
    }

    for ($i = 0; $i < $range; $i++) {
        yield \random_int(1, \PHP_INT_MAX);
    }
}

function realNumbers(int $range = 100): \Generator
{
    if ($range < 1) {
        throw new LogicException;
    }

    for ($i = 0; $i < $range; $i++) {
        $int = \random_int(\PHP_INT_MIN, \PHP_INT_MAX);
        $sub = \random_int(0, \PHP_INT_MAX);

        yield (float) "$int.$sub";
    }
}

function range(float $min, float $max, float $step = 1): \Generator
{
    yield from \range($min, $max, $step);
}

function chars(): \Generator
{
    foreach (\range(0, 255) as $i) {
        yield chr($i);
    }
}

function strings(int $range = 100, int $maxLength = 128): \Generator
{
    if ($range < 1) {
        throw new LogicException;
    }

    for ($i = 0; $i < $range; $i++) {
        $string = '';

        foreach (\range(1, \random_int(2, $maxLength)) as $_) {
            $string .= chr(\random_int(0, 255));
        }

        yield $string;
    }
}

/**
 * @see https://github.com/minimaxir/big-list-of-naughty-strings
 */
function unsafeStrings(): \Generator
{
    yield from Json::decode(
        \file_get_contents(__DIR__.'/unsafeStrings.json')
    );
}

function mixed(): \Generator
{
    yield from [
        'mixed',
        -42,
        0,
        42,
        -13.37,
        13.37,
        'foobar',
        [],
        ['foo'],
        ['foo' => 'bar'],
        new class {},
        function() {},
    ];
}
