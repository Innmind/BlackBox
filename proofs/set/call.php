<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Random,
};

return static function() {
    yield test(
        'Set\Call always return a new value',
        static function($assert) {
            $set = Set\Call::of(static fn() => new \stdClass);

            $assert
                ->expected($set->values(Random::default)->current()->unwrap())
                ->not()
                ->same($set->values(Random::default)->current()->unwrap());
        },
    );
};
