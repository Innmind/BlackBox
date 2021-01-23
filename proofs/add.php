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
        when(function($a, $b) {
            return add($a, $b);
        }),
        then(
            hold(function($held, $fail, $result, $a, $b) {
                if (!$result->value() === add($b, $a)) {
                    $fail('add is not commutative');
                }

                $held();
            }),
        ),
    );

    yield proof(
        'add is associative',
        given(
            Set\Integers::any(),
            Set\Integers::any(),
            Set\Integers::any(),
        ),
        when(function($a, $b, $c) {
            return add(add($a, $b), $c);
        }),
        then(
            hold(function($held, $fail, $result, $a, $b, $c) {
                if (!$result->value() === add($a, add($b, $c))) {
                    $fail('add is not associative');
                }

                $held();
            }),
        ),
    );

    yield proof(
        'add is an identity function',
        given(
            Set\Integers::any(),
        ),
        when(function($a) {
            return add($a, 0);
        }),
        then(
            hold(function($held, $fail, $result, $a) {
                if (!$result->value() === $a) {
                    $fail('add is not an identity function');
                }

                $held();
            }),
        ),
    );
};
