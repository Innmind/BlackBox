<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Tag,
};

return static function($prove) {
    yield $prove
        ->proof('Set::strings()->mutuallyExclusive()')
        ->given(Set::strings()->mutuallyExclusive(
            Set::strings()->madeOf(Set::strings()->unicode()->char(), Set::strings()->chars()),
            Set::strings()->madeOf(Set::strings()->unicode()->char(), Set::strings()->chars()),
            Set::strings()->madeOf(Set::strings()->unicode()->char(), Set::strings()->chars()),
            Set::strings()->madeOf(Set::strings()->unicode()->char(), Set::strings()->chars()),
        ))
        ->test(static function($assert, $values) {
            [$a, $b, $c, $d] = \array_map(\strtolower(...), $values);

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
        })
        ->tag(Tag::ci, Tag::local);
};
