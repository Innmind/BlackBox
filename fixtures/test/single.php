<?php
declare(strict_types = 1);

use function Innmind\BlackBox\{
    test,
    given,
    when,
    then,
};
use Innmind\BlackBox\{
    Set,
    Assert,
};

return test(
    'add',
    given(),
    when(static function() {
        return 42;
    }),
    then(
        Assert\int()
    )
);
