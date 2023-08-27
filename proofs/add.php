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

return static function() {
    yield proof(
        'add is commutative',
        given(
            Set\Integers::any(),
            Set\Integers::any(),
        ),
        static fn($assert, $a, $b) => $assert->same(add($a, $b), add($b, $a)),
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'add is associative',
        given(
            Set\Integers::any(),
            Set\Integers::any(),
            Set\Integers::any(),
        ),
        static fn($assert, $a, $b, $c) => $assert->same(
            add(add($a, $b), $c),
            add($a, add($b, $c)),
        ),
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'add is an identity function',
        given(Set\Integers::any()),
        static fn($assert, $a) => $assert->same((string) $a, add($a, 0)),
    )->tag(Tag::ci, Tag::local);
};
