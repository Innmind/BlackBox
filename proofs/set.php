<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Random,
    Tag,
};

return static function() {
    $anySet = Set::of(
        Set::integers()->toSet(),
        // real numbers not used as it may return the type integer
        Set::strings()->toSet(),
        Set::sequence(Set::either(
            Set::integers()->toSet(),
            // real numbers not used as it may return the type integer
            Set::strings()->toSet(),
        ))->between(0, 10)->toSet(),
        Set::of(true, false),
    );

    yield proof(
        'Set::flatMap() input is of the type of the parent',
        given($anySet, $anySet),
        static function($assert, $input, $output) {
            $compose = $input->flatMap(static function($value) use ($assert, $input, $output) {
                $assert->same(
                    \gettype($value),
                    \gettype($input->values(Random::default)->current()->unwrap()),
                );

                return $output;
            });

            foreach ($compose->values(Random::default) as $_) {
                // triggers the assert in the flatMap lambda
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Set::flatMap() values is of the type of the child set',
        given($anySet, $anySet),
        static function($assert, $input, $output) {
            $compose = $input->flatMap(static fn() => $output);

            foreach ($compose->values(Random::default) as $value) {
                $assert->same(
                    \gettype($value->unwrap()),
                    \gettype($output->values(Random::default)->current()->unwrap()),
                );
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Set::flatMap()->take()',
        given($anySet, $anySet, Set::integers()->between(1, 100)),
        static function($assert, $input, $output, $size) {
            $compose = $input
                ->flatMap(static fn() => $output)
                ->take($size);
            $count = 0;

            foreach ($compose->values(Random::default) as $_) {
                ++$count;
            }

            $assert->same($size, $count);
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Set::flatMap()->filter() is applied on the child values',
        given($anySet, $anySet),
        static function($assert, $input, $output) {
            $expected = \gettype($output->values(Random::default)->current()->unwrap());
            $compose = $input
                ->flatMap(static fn() => $output)
                ->filter(static function($value) use ($assert, $expected) {
                    $assert->same(
                        $expected,
                        \gettype($value),
                    );

                    return true;
                });

            foreach ($compose->values(Random::default) as $_) {
                // triggers the assert in the filter lambda
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Set::flatMap()->map()',
        given($anySet, Set::of(Set::integers()->between(-1_000_000, 1_000_000))),
        static function($assert, $input, $output) {
            $compose = $input
                ->flatMap(static fn() => $output)
                ->map(static fn($i) => $i*2);

            foreach ($compose->values(Random::default) as $value) {
                $assert->same(
                    0,
                    $value->unwrap() % 2,
                );
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Set::flatMap() input value is always the same',
        given(
            Set::of(
                Set::integers()->toSet(),
                Set::realNumbers()->toSet(),
                Set::strings()->toSet(),
                Set::sequence(Set::either(
                    Set::integers()->toSet(),
                    Set::realNumbers()->toSet(),
                    Set::strings()->toSet(),
                ))->between(0, 10)->toSet(),
            ),
            $anySet,
        ),
        static function($assert, $input, $output) {
            $compose = $input->flatMap(static fn($value) => $output->map(
                static fn() => $value,
            ));
            $values = [];

            foreach ($compose->values(Random::default) as $value) {
                $values[] = $value->unwrap();
            }

            $assert->count(
                1,
                \array_unique($values, \SORT_REGULAR),
            );
        },
    )->tag(Tag::ci, Tag::local);
};
