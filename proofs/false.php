<?php

use Innmind\BlackBox\Set;

function add($a, $b)
{
    return $a + $b;
}

return function() {
    yield proof(
        'add is always positive',
        given(
            Set\Integers::any(),
            Set\Integers::any(),
        ),
        when(fn($a, $b) => add($a, $b)),
        then(
            greaterThanOrEqual(0),
        ),
    );
};
