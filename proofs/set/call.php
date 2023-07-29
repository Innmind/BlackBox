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
            $set = Set\Call::of(static fn() => new \stdClass)->values(Random::default);
            $current = $set->current()->unwrap();
            $set->next();

            $assert
                ->expected($current)
                ->not()
                ->same($set->current()->unwrap());
        },
    );
    yield test(
        'Set\Call regenerate the value when shrinking',
        static function($assert) {
            $set = Set\Call::of(static fn() => new \stdClass)->values(Random::default);
            $current = $set->current();

            $assert
                ->expected($current->unwrap())
                ->not()
                ->same($current->shrink()->a()->unwrap());
        },
    );
};