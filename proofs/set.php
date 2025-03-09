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
                    \gettype($value->unwrap()),
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
                Set::integers(),
                Set::realNumbers(),
                Set::strings(),
                Set::sequence(Set::either(
                    Set::integers(),
                    Set::realNumbers(),
                    Set::strings(),
                ))->between(0, 10),
            ),
            $anySet,
        ),
        static function($assert, $input, $output) {
            $compose = $input->flatMap(static fn($value) => $output->map(
                static fn() => $value->unwrap(),
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

    yield test(
        'Set::flatMap() input value is shrinkable',
        static function($assert) {
            $compose = Set::integers()->flatMap(
                static fn($seed) => Set::strings()->map(
                    static fn($string) => $seed->map(
                        static fn($i) => $i.$string,
                    ),
                ),
            );

            // The calls to unwrap below are here to simulate the fact that a
            // value is first unwrapped to be tested before eventually being
            // shrunk in case of a test failure.
            foreach ($compose->values(Random::default) as $value) {
                $value->unwrap();

                while ($value->shrinkable()) {
                    $value = $value->shrink()->a();
                    $value->unwrap();
                }

                $assert->same('0', $value->unwrap());
            }

            $compose = Set::strings()->flatMap(
                static fn($seed) => Set::compose(
                    static fn($a, $b) => $seed->map(
                        static fn($string) => $a.$string.$b,
                    ),
                    Set::integers(),
                    Set::integers(),
                ),
            );

            foreach ($compose->values(Random::default) as $value) {
                $value->unwrap();

                while ($value->shrinkable()) {
                    $value = $value->shrink()->a();
                    $value->unwrap();
                }

                $assert->same('00', $value->unwrap());
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Set::flatMap() input value is composable and shrinkable',
        static function($assert) {
            $compose = Set::strings()->flatMap(
                static fn($stringSeed) => Set::integers()->flatMap(
                    static fn($aSeed) => Set::integers()->map(
                        static fn($b) => $stringSeed
                            ->flatMap(
                                static fn($string) => $aSeed->map(
                                    static fn($a) => $a.'|'.$string.'|'.$b,
                                ),
                            )
                            ->map(static fn($string) => "($string)"),
                    ),
                ),
            );

            // The calls to unwrap below are here to simulate the fact that a
            // value is first unwrapped to be tested before eventually being
            // shrunk in case of a test failure.
            foreach ($compose->values(Random::default) as $value) {
                $value->unwrap();

                while ($value->shrinkable()) {
                    $value = $value->shrink()->a();
                    $value->unwrap();
                }

                $assert->same('(0||0)', $value->unwrap());
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Set::compose() can collapse seeds from flatMaps',
        static function($assert) {
            $compose = Set::strings()->flatMap(
                static fn($stringSeed) => Set::integers()->flatMap(
                    static fn($aSeed) => Set::compose(
                        static fn($b, $stringB) => $stringSeed->flatMap(
                            static fn($string) => $aSeed->map(
                                static fn($a) => $a.'|'.$string.'|'.$stringB.'|'.$b,
                            ),
                        ),
                        Set::integers(),
                        Set::strings(),
                    ),
                ),
            );

            // The calls to unwrap below are here to simulate the fact that a
            // value is first unwrapped to be tested before eventually being
            // shrunk in case of a test failure.
            foreach ($compose->values(Random::default) as $value) {
                $value->unwrap();

                while ($value->shrinkable()) {
                    $value = $value->shrink()->a();
                    $value->unwrap();
                }

                $assert->same('0|||0', $value->unwrap());
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Set::generator() can collapse seeds from flatMaps',
        static function($assert) {
            $compose = Set::strings()->flatMap(
                static fn($stringSeed) => Set::generator(static function() use ($stringSeed) {
                    yield $stringSeed;
                }),
            );

            // The calls to unwrap below are here to simulate the fact that a
            // value is first unwrapped to be tested before eventually being
            // shrunk in case of a test failure.
            foreach ($compose->values(Random::default) as $value) {
                $value->unwrap();

                while ($value->shrinkable()) {
                    $value = $value->shrink()->a();
                    $value->unwrap();
                }

                $assert->same('', $value->unwrap());
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Set::flatMap()->map()->filter()',
        static function($assert) {
            $compose = Set::integers()->flatMap(
                static fn($seed) => Set::strings()
                    ->map(static fn($string) => $seed->map(
                        static fn($i) => $i.$string,
                    ))
                    ->filter(static fn($string) => $string !== '0'),
            );

            // The calls to unwrap below are here to simulate the fact that a
            // value is first unwrapped to be tested before eventually being
            // shrunk in case of a test failure.
            foreach ($compose->values(Random::default) as $value) {
                $value->unwrap();

                while ($value->shrinkable()) {
                    $value = $value->shrink()->a();
                    $value->unwrap();
                }

                $assert
                    ->array(['-1', '1'])
                    ->contains($value->unwrap());
            }
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Set::flatMap()->map()->filter() on composed seeds',
        static function($assert) {
            $compose = Set::integers()->flatMap(
                static fn($seedA) => Set::integers()->flatMap(
                    static fn($seedB) => Set::strings()
                        ->map(static fn($string) => $seedA->flatMap(
                            static fn($a) => $seedB->map(
                                static fn($b) => $a.$string.$b,
                            ),
                        ))
                        ->filter(static fn($string) => $string !== '00'),
                ),
            );

            // The calls to unwrap below are here to simulate the fact that a
            // value is first unwrapped to be tested before eventually being
            // shrunk in case of a test failure.
            // The take(10) is here to speed things up as the default would need
            // to shrink 300 values (both ints and the string) to their minimum
            // values. It would take almost a minute.
            foreach ($compose->take(10)->values(Random::default) as $value) {
                $value->unwrap();

                while ($value->shrinkable()) {
                    $value = $value->shrink()->a();
                    $value->unwrap();
                }

                $assert
                    ->array(['0-1', '-10', '01', '10'])
                    ->contains($value->unwrap());
            }
        },
    )->tag(Tag::ci, Tag::local);
};
