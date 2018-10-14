<?php
declare(strict_types = 1);

use function Innmind\BlackBox\{
    test,
    given,
    when,
    then,
    any,
    value,
    generate,
};
use Innmind\BlackBox\{
    Set,
    Assert,
};

return (function() {
    yield test(
        'constant',
        given(),
        when(static function() {
            return 42;
        }),
        then(Assert\int())
    );

    yield test(
        'divide',
        given(
            any('a', Set\integers(100)),
            any('b', Set\integersExceptZero(100))
        ),
        when(static function($given) {
            return $given->a / $given->b;
        }),
        then(Assert\int())
    );
})();
