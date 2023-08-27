<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Tag,
};

return static function() {
    yield proof(
        'Set\Slice',
        given(
            Set\Sequence::of(Set\Type::any())->atLeast(11),
            Set\Slice::between(10, 20),
        ),
        static function($assert, $values, $slice) {
            $subset = $slice($values);
            $prefix = \array_slice($values, 0, 10);

            $assert
                ->expected($prefix)
                ->not()
                ->same(\array_slice($subset, 0, 10));

            foreach ($subset as $value) {
                $assert
                    ->expected($value)
                    ->in($values);
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Set\Slice min length',
        given(
            Set\Sequence::of(Set\Type::any())->atLeast(2),
            Set\Slice::any()->atLeast(2),
        ),
        static function($assert, $values, $slice) {
            $subset = $slice($values);

            $assert
                ->number(\count($subset))
                ->greaterThanOrEqual(2);
        },
    )->tag(Tag::ci, Tag::local);
};
