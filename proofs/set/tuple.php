<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Random,
    Tag,
};

return static function() {
    yield proof(
        'Set\Tuple values always contain the same number of elements as sets',
        given(
            Set::sequence(Set::integers())
                ->between(2, 10)
                ->map(static fn($values) => \array_map(
                    static fn() => Set::type(),
                    $values,
                )),
        ),
        static function($assert, $sets) {
            $set = Set\Tuple::of(...$sets)
                ->take(10) // to speed things up
                ->values(Random::default);

            foreach ($set as $value) {
                $assert->count(
                    \count($sets),
                    $value->unwrap(),
                );
            }
        },
    )->tag(Tag::ci, Tag::local);
};
