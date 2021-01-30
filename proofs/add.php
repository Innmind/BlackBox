<?php

use Innmind\BlackBox\Set;

function add($a, $b)
{
    return $a + $b;
}

return function() {
    yield proof(
        'add is commutative',
        given(
            Set\Integers::any(),
            Set\Integers::any(),
        ),
        when(fn($a, $b) => add($a, $b)),
        then(
            same(
                fn($a, $b) => add($b, $a),
                'add is not commutative'
            ),
        ),
    );

    yield proof(
        'add is associative',
        given(
            Set\Integers::any(),
            Set\Integers::any(),
            Set\Integers::any(),
        ),
        when(fn($a, $b, $c) => add(add($a, $b), $c)),
        then(
            same(
                fn($a, $b, $c) => add($a, add($b, $c)),
                'add is not associative',
            ),
        ),
    );

    yield proof(
        'add is an identity function',
        given(Set\Integers::any()),
        when(fn($a) => add($a, 0)),
        then(
            same(
                fn($a) => $a,
                'add is not an identity function',
            ),
        ),
    );
};
