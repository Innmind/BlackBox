<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Random,
    Tag,
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
    )->tag(Tag::ci, Tag::local);
    yield test(
        'Set\Call is not shrinkable',
        static function($assert) {
            $set = Set\Call::of(static fn() => new \stdClass)->values(Random::default);
            $current = $set->current();

            $assert->false($current->shrinkable());
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Set\Call regenerate the value each time it is accessed',
        static function($assert) {
            $set = Set\Call::of(static fn() => new \stdClass)->values(Random::default);
            $current = $set->current();

            $assert
                ->expected($current->unwrap())
                ->not()
                ->same($current->unwrap());
        },
    )->tag(Tag::ci, Tag::local);
};
