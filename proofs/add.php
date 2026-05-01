<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Tag,
};

function add($a, $b): string
{
    return \gmp_strval(\gmp_add($a, $b));
}

return static function($prove) {
    yield $prove
        ->proof('add is commutative')
        ->given(
            Set::integers(),
            Set::integers(),
        )
        ->test(static fn($assert, $a, $b) => $assert->same(add($a, $b), add($b, $a)))
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('add is associative')
        ->given(
            Set::integers(),
            Set::integers(),
            Set::integers(),
        )
        ->test(static fn($assert, $a, $b, $c) => $assert->same(
            add(add($a, $b), $c),
            add($a, add($b, $c)),
        ))
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('add is an identity function')
        ->given(Set::integers())
        ->test(static fn($assert, $a) => $assert->same((string) $a, add($a, 0)))
        ->tag(Tag::ci, Tag::local);
};
