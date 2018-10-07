<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Exception\LogicException;
use Innmind\Json\Json;
use Innmind\Immutable\{
    SetInterface,
    Set,
};

function integers(int $range = 1000): SetInterface
{
    if ($range < 1) {
        throw new LogicException;
    }

    $set = Set::of('int');

    while ($set->size() < $range) {
        $int = \random_int(\PHP_INT_MIN, \PHP_INT_MAX);

        if ($int === 0) {
            continue;
        }

        $set = $set->add($int);
    }

    return $set;
}

function integersExceptZero(int $range = 1000): SetInterface
{
    if ($range < 1) {
        throw new LogicException;
    }

    $set = Set::of('int');

    while ($set->size() < $range) {
        $int = \random_int(\PHP_INT_MIN, \PHP_INT_MAX);

        if ($int === 0) {
            continue;
        }

        $set = $set->add($int);
    }

    return $set;
}

function naturalNumbers(int $range = 1000): SetInterface
{
    if ($range < 1) {
        throw new LogicException;
    }

    $set = Set::of('int');

    while ($set->size() < $range) {
        $set = $set->add(\random_int(0, \PHP_INT_MAX));
    }

    return $set;
}

function naturalNumbersExceptZero(int $range = 1000): SetInterface
{
    if ($range < 1) {
        throw new LogicException;
    }

    $set = Set::of('int');

    while ($set->size() < $range) {
        $set = $set->add(\random_int(1, \PHP_INT_MAX));
    }

    return $set;
}

function realNumbers(int $range = 1000): SetInterface
{
    if ($range < 1) {
        throw new LogicException;
    }

    $set = Set::of('float');

    while ($set->size() < $range) {
        $int = \random_int(\PHP_INT_MIN, \PHP_INT_MAX);
        $sub = \random_int(0, \PHP_INT_MAX);

        $set = $set->add((float) "$int.$sub");
    }

    return $set;
}

function range(float $min, float $max, float $step = 1): SetInterface
{
    return Set::of('float', ...\range($min, $max, $step));
}

function char(): SetInterface
{
    return Set::of('string', chr(\random_int(0, 255)));
}

function strings(int $range = 1000, int $maxLength = 512): SetInterface
{
    $set = Set::of('string');

    foreach (range(1, $range) as $_) {
        $string = '';

        foreach (range(1, \random_int(2, $maxLength)) as $_) {
            $string .= chr(\random_int(0, 255));
        }

        $set = $set->add($string);
    }

    return $set;
}

/**
 * @see https://github.com/minimaxir/big-list-of-naughty-strings
 */
function unsafeStrings(): SetInterface
{
    return Set::of('string', ...Json::decode(
        \file_get_contents(__DIR__.'/unsafeStrings.json')
    ));
}
