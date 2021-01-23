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
        when(function($a, $b) {
            return add($a, $b);
        }),
        then(
            hold(function($held, $fail, $result) {
                if ($result->value() < 0) {
                    $fail('add is not always positive');
                }

                $held();
            }),
        ),
    );
};
