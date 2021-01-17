<?php

use Innmind\BlackBox\{
    Runner\Proof,
    Runner\Given,
    Runner\When,
    Runner\Then,
    Runner\Hold,
    Set,
};

function add($a, $b)
{
    return $a + $b;
}

return function() {
    yield new Proof(
        'add is commutative',
        new Given(
            Set\Integers::any(),
            Set\Integers::any(),
        ),
        new When(function($a, $b) {
            return add($a, $b);
        }),
        new Then(
            new Hold(function($held, $fail, $result, $a, $b) {
                if (!$result->value() === add($b, $a)) {
                    $fail('add is not commutative');
                }

                $held();
            }),
        ),
    );

    yield new Proof(
        'add is associative',
        new Given(
            Set\Integers::any(),
            Set\Integers::any(),
            Set\Integers::any(),
        ),
        new When(function($a, $b, $c) {
            return add(add($a, $b), $c);
        }),
        new Then(
            new Hold(function($held, $fail, $result, $a, $b, $c) {
                if (!$result->value() === add($a, add($b, $c))) {
                    $fail('add is not associative');
                }

                $held();
            }),
        ),
    );

    yield new Proof(
        'add is an identity function',
        new Given(
            Set\Integers::any(),
        ),
        new When(function($a) {
            return add($a, 0);
        }),
        new Then(
            new Hold(function($held, $fail, $result, $a) {
                if (!$result->value() === $a) {
                    $fail('add is not an identity function');
                }

                $held();
            }),
        ),
    );
};
