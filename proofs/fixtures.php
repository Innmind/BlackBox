<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Tag,
};
use Fixtures\Innmind\BlackBox\{
    Counter,
    DownAndUpIsAnIdentityFunction,
    DownChangeState,
    LowerBoundAtZero,
    RaiseBy,
    UpAndDownIsAnIdentityFunction,
    UpChangeState,
    UpperBoundAtHundred,
};

return static function() {
    yield property(
        DownAndUpIsAnIdentityFunction::class,
        Set\Decorate::mutable(
            static fn($initial) => new Counter($initial),
            Set\Integers::between(1, 100),
        ),
    )->tag(Tag::ci, Tag::local);

    yield property(
        DownChangeState::class,
        Set\Decorate::mutable(
            static fn($initial) => new Counter($initial),
            Set\Integers::between(1, 100),
        ),
    )->tag(Tag::ci, Tag::local);

    yield property(
        LowerBoundAtZero::class,
        Set\Elements::of(new Counter),
    )->tag(Tag::ci, Tag::local);

    yield property(
        RaiseBy::class,
        Set\Decorate::mutable(
            static fn($initial) => new Counter($initial),
            Set\Integers::between(0, 99),
        ),
    )->tag(Tag::ci, Tag::local);

    yield property(
        UpAndDownIsAnIdentityFunction::class,
        Set\Decorate::mutable(
            static fn($initial) => new Counter($initial),
            Set\Integers::between(0, 98),
        ),
    )->tag(Tag::ci, Tag::local);

    yield property(
        UpChangeState::class,
        Set\Decorate::mutable(
            static fn($initial) => new Counter($initial),
            Set\Integers::between(0, 99),
        ),
    )->tag(Tag::ci, Tag::local);

    yield property(
        UpperBoundAtHundred::class,
        Set\Elements::of(new Counter(99), new Counter(100)),
    )->tag(Tag::ci, Tag::local);

    yield properties(
        'Counter properties',
        Set\Properties::any(
            DownAndUpIsAnIdentityFunction::any(),
            DownChangeState::any(),
            LowerBoundAtZero::any(),
            RaiseBy::any(),
            UpAndDownIsAnIdentityFunction::any(),
            UpChangeState::any(),
            UpperBoundAtHundred::any(),
        ),
        Set\Decorate::mutable(
            static fn($initial) => new Counter($initial),
            Set\Integers::between(0, 100),
        ),
    )->tag(Tag::ci, Tag::local);
};
