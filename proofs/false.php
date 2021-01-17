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
        'add is always positive',
        new Given(
            Set\Integers::any(),
            Set\Integers::any(),
        ),
        new When(function($a, $b) {
            return add($a, $b);
        }),
        new Then(
            new Hold(function($held, $fail, $result) {
                if ($result->value() < 0) {
                    $fail('add is not always positive');
                }

                $held();
            }),
        ),
    );
};
