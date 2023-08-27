<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Tag,
};

return static function() {
    yield proof(
        'Set\MutuallyExclusive',
        given(Set\MutuallyExclusive::of(
            Set\Strings::madeOf(Set\Unicode::any(), Set\Chars::any()),
            Set\Strings::madeOf(Set\Unicode::any(), Set\Chars::any()),
            Set\Strings::madeOf(Set\Unicode::any(), Set\Chars::any()),
            Set\Strings::madeOf(Set\Unicode::any(), Set\Chars::any()),
        )),
        static function($assert, $values) {
            [$a, $b, $c, $d] = $values;

            $assert
                ->string($a)
                ->not()
                ->contains($b)
                ->contains($c)
                ->contains($d);
            $assert
                ->string($b)
                ->not()
                ->contains($a)
                ->contains($c)
                ->contains($d);
            $assert
                ->string($c)
                ->not()
                ->contains($a)
                ->contains($b)
                ->contains($d);
            $assert
                ->string($d)
                ->not()
                ->contains($a)
                ->contains($b)
                ->contains($c);
        },
    )->tag(Tag::ci, Tag::local);
};
