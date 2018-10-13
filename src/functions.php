<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Given;
use Innmind\Immutable\{
    SetInterface,
    Set,
};

function given(Given\InitialValue ...$initialValues): Given
{
    return new Given(...$initialValues);
}

function any(string $name, SetInterface $set): Given\InitialValue
{
    return new Given\Any(
        new Given\InitialValue\Name($name),
        $set
    );
}

function value(string $name, $value): Given\InitialValue
{
    return any($name, Set::of('mixed', $value));
}

function generate(string $name, callable $generate): Given\InitialValue
{
    return new Given\Generate(
        new Given\InitialValue\Name($name),
        $generate
    );
}
