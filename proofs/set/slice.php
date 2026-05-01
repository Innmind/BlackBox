<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Tag,
};

return static function($prove) {
    yield $prove
        ->proof('Set\Slice')
        ->given(
            Set::sequence(Set::type())->atLeast(11),
            Set\Slice::between(10, 20),
        )
        ->test(static function($assert, $values, $slice) {
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
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Set\Slice min length')
        ->given(
            Set::sequence(Set::type())->atLeast(2),
            Set\Slice::any()->atLeast(2),
        )
        ->test(static function($assert, $values, $slice) {
            $subset = $slice($values);

            $assert
                ->number(\count($subset))
                ->greaterThanOrEqual(2);
        })
        ->tag(Tag::ci, Tag::local);
};
