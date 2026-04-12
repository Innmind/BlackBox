<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Random,
    Tag,
};
use function Innmind\BlackBox\Runner\test;

return static function() {
    yield test(
        'Set::call() always return a new value',
        static function($assert) {
            $set = Set::call(static fn() => new stdClass)->values(Random::default);
            $current = $set->current()->unwrap();
            $set->next();

            $assert
                ->expected($current)
                ->not()
                ->same($set->current()->unwrap());
        },
    )->tag(Tag::ci, Tag::local);
    yield test(
        'Set::call() is not shrinkable',
        static function($assert) {
            $set = Set::call(static fn() => new stdClass)->values(Random::default);
            $current = $set->current();

            $assert->null($current->shrink());
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Set::call() regenerate the value each time it is accessed',
        static function($assert) {
            $set = Set::call(static fn() => new stdClass)->values(Random::default);
            $current = $set->current();

            $assert
                ->expected($current->unwrap())
                ->not()
                ->same($current->unwrap());
        },
    )->tag(Tag::ci, Tag::local);
};
