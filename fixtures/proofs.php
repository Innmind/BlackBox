<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Tag,
    Application,
    Runner\IO\Collect,
};
use function Innmind\BlackBox\Runner\{
    property,
    properties,
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

return static function($prove) {
    yield $prove
        ->property(
            DownAndUpIsAnIdentityFunction::class,
            Set::integers()
                ->between(1, 100)
                ->map(static fn($initial) => static fn() => new Counter($initial)),
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->property(
            DownChangeState::class,
            Set::integers()
                ->between(1, 100)
                ->map(static fn($initial) => static fn() => new Counter($initial)),
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->property(
            LowerBoundAtZero::class,
            Set::of(static fn() => new Counter),
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->property(
            RaiseBy::class,
            Set::integers()
                ->between(0, 99)
                ->map(static fn($initial) => static fn() => new Counter($initial)),
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->property(
            UpAndDownIsAnIdentityFunction::class,
            Set::integers()
                ->between(0, 98)
                ->map(static fn($initial) => static fn() => new Counter($initial)),
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->property(
            UpChangeState::class,
            Set::integers()
                ->between(0, 99)
                ->map(static fn($initial) => static fn() => new Counter($initial)),
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->property(
            UpperBoundAtHundred::class,
            Set::of(
                static fn() => new Counter(99),
                static fn() => new Counter(100),
            ),
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->properties(
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
            Set::integers()
                ->between(0, 100)
                ->map(static fn($initial) => static fn() => new Counter($initial)),
        )
        ->tag(Tag::ci, Tag::local);
};
