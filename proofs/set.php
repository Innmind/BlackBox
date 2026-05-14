<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Random,
    Tag,
    Exception\EmptySet,
};

return static function($prove) {
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

    yield $prove
        ->proof('Set::flatMap() input is of the type of the parent')
        ->given($anySet, $anySet)
        ->test(static function($assert, $input, $output) {
            $compose = $input->flatMap(static function($value) use ($assert, $input, $output) {
                $assert->same(
                    \gettype($value->unwrap()),
                    \gettype($input->values(Random::default)->current()->unwrap()),
                );

                return $output;
            });

            foreach ($compose->take(100)->values(Random::default) as $_) {
                // triggers the assert in the flatMap lambda
            }
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Set::flatMap() values is of the type of the child set')
        ->given($anySet, $anySet)
        ->test(static function($assert, $input, $output) {
            $compose = $input->flatMap(static fn() => $output);

            foreach ($compose->take(100)->values(Random::default) as $value) {
                $assert->same(
                    \gettype($value->unwrap()),
                    \gettype($output->values(Random::default)->current()->unwrap()),
                );
            }
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Set::flatMap()->take()')
        ->given($anySet, $anySet, Set::integers()->between(1, 100))
        ->test(static function($assert, $input, $output, $size) {
            $compose = $input
                ->flatMap(static fn() => $output)
                ->take($size);
            $count = 0;

            foreach ($compose->values(Random::default) as $_) {
                ++$count;
            }

            $assert->same($size, $count);
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Set::flatMap()->filter() is applied on the child values')
        ->given($anySet, $anySet)
        ->test(static function($assert, $input, $output) {
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

            foreach ($compose->take(100)->values(Random::default) as $_) {
                // triggers the assert in the filter lambda
            }
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Set::flatMap()->map()')
        ->given($anySet, Set::of(Set::integers()->between(-1_000_000, 1_000_000)))
        ->test(static function($assert, $input, $output) {
            $compose = $input
                ->flatMap(static fn() => $output)
                ->map(static fn($i) => $i*2);

            foreach ($compose->take(100)->values(Random::default) as $value) {
                $assert->same(
                    0,
                    $value->unwrap() % 2,
                );
            }
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Set::flatMap() input value is always the same')
        ->given(
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
        )
        ->test(static function($assert, $input, $output) {
            $compose = $input->flatMap(static fn($value) => $output->map(
                static fn() => $value->unwrap(),
            ));
            $values = [];

            foreach ($compose->take(100)->values(Random::default) as $value) {
                $values[] = $value->unwrap();
            }

            $assert->count(
                1,
                \array_unique($values, \SORT_REGULAR),
            );
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Set::flatMap() input value is shrinkable',
            static function($assert) {
                $compose = Set::integers()->flatMap(
                    static fn($seed) => Set::compose(
                        static fn($string, $i) => $i.$string,
                        Set::strings(),
                        $seed->toSet(),
                    ),
                );

                // The calls to unwrap below are here to simulate the fact that a
                // value is first unwrapped to be tested before eventually being
                // shrunk in case of a test failure.
                foreach ($compose->take(100)->values(Random::default) as $value) {
                    $value->unwrap();

                    while ($shrunk = $value->shrink()) {
                        $value = $shrunk->a();
                        $value->unwrap();
                    }

                    $assert->same('0', $value->unwrap());
                }

                $compose = Set::strings()->flatMap(
                    static fn($seed) => Set::compose(
                        static fn($a, $string, $b) => $a.$string.$b,
                        Set::integers(),
                        $seed->toSet(),
                        Set::integers(),
                    ),
                );

                foreach ($compose->take(100)->values(Random::default) as $value) {
                    $value->unwrap();

                    while ($shrunk = $value->shrink()) {
                        $value = $shrunk->a();
                        $value->unwrap();
                    }

                    $assert->same('00', $value->unwrap());
                }
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Set::flatMap() input value is composable and shrinkable',
            static function($assert) {
                $compose = Set::strings()->flatMap(
                    static fn($stringSeed) => Set::integers()->flatMap(
                        static fn($aSeed) => Set::compose(
                            static fn($a, $string, $b) => $a.'|'.$string.'|'.$b,
                            $aSeed->toSet(),
                            $stringSeed->toSet(),
                            Set::integers(),
                        )
                            ->map(static fn($string) => "($string)"),
                    ),
                );

                // The calls to unwrap below are here to simulate the fact that a
                // value is first unwrapped to be tested before eventually being
                // shrunk in case of a test failure.
                foreach ($compose->take(100)->values(Random::default) as $value) {
                    $value->unwrap();

                    while ($shrunk = $value->shrink()) {
                        $value = $shrunk->a();
                        $value->unwrap();
                    }

                    $assert->same('(0||0)', $value->unwrap());
                }
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Set::compose() can collapse seeds from flatMaps',
            static function($assert) {
                $compose = Set::strings()->flatMap(
                    static fn($stringSeed) => Set::integers()->flatMap(
                        static fn($aSeed) => Set::compose(
                            static fn($a, $b, $string, $stringB) => $a.'|'.$string.'|'.$stringB.'|'.$b,
                            $aSeed->toSet(),
                            Set::integers(),
                            $stringSeed->toSet(),
                            Set::strings(),
                        ),
                    ),
                );

                // The calls to unwrap below are here to simulate the fact that a
                // value is first unwrapped to be tested before eventually being
                // shrunk in case of a test failure.
                foreach ($compose->take(100)->values(Random::default) as $value) {
                    $value->unwrap();

                    while ($shrunk = $value->shrink()) {
                        $value = $shrunk->a();
                        $value->unwrap();
                    }

                    $assert->same('0|||0', $value->unwrap());
                }
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Set::flatMap()->map()->filter()',
            static function($assert) {
                $compose = Set::integers()->flatMap(
                    static fn($seed) => Set::compose(
                        static fn($string, $i) => $i.$string,
                        Set::strings(),
                        $seed->toSet(),
                    )
                        ->filter(static fn($string) => $string !== '0'),
                );

                // The calls to unwrap below are here to simulate the fact that a
                // value is first unwrapped to be tested before eventually being
                // shrunk in case of a test failure.
                foreach ($compose->take(100)->values(Random::default) as $value) {
                    $value->unwrap();

                    while ($shrunk = $value->shrink()) {
                        $value = $shrunk->a();
                        $value->unwrap();
                    }

                    $assert
                        ->array(['-1', '1'])
                        ->contains($value->unwrap());
                }
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Set::flatMap()->map()->filter() on composed seeds',
            static function($assert) {
                $compose = Set::integers()->flatMap(
                    static fn($seedA) => Set::integers()->flatMap(
                        static fn($seedB) => Set::compose(
                            static fn($a, $string, $b) => $a.$string.$b,
                            $seedA->toSet(),
                            Set::strings(),
                            $seedB->toSet(),
                        )
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

                    while ($shrunk = $value->shrink()) {
                        $value = $shrunk->a();
                        $value->unwrap();
                    }

                    $assert
                        ->array(['0-1', '-10', '01', '10'])
                        ->contains($value->unwrap());
                }
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Set::compose() should shrink seeded values and apply filters',
            static function($assert) {
                $compose = Set::strings()->madeOf(Set::of('a'))->flatMap(
                    static fn($seed) => Set::compose(
                        static fn($a, $string, $b) => $a.'|'.$string.'|'.$b,
                        Set::integers()->above(0), // to simplify the assertion
                        $seed->toSet(),
                        Set::integers()->above(0),
                    )->filter(static fn($string) => $string !== '0||0'),
                );

                // The calls to unwrap below are here to simulate the fact that a
                // value is first unwrapped to be tested before eventually being
                // shrunk in case of a test failure.
                // The take(10) is here to speed things up as the default would need
                // to shrink 300 values (both ints and the string) to their minimum
                // values. It would take almost a minute.
                foreach ($compose->take(10)->values(Random::default) as $value) {
                    $value->unwrap();

                    while ($shrunk = $value->shrink()) {
                        $value = $shrunk->a();
                        $value->unwrap();
                    }

                    $assert
                        ->array(['0|a|0', '0||1', '1||0'])
                        ->contains($value->unwrap());
                }
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Set->take()->enumerate()')
        ->given(
            $anySet,
            Set::integers()->between(1, 1_000),
        )
        ->test(static function($assert, $set, $size) {
            $values = \iterator_to_array($set->take($size)->enumerate());

            $assert->count($size, $values);
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Set->enumerate() contains the expressed type')
        ->given(Set::of(
            [[true, false], Set::of(true, false)],
            [\range(0, 101), Set::integers()->between(0, 100)->toSet()],
        ))
        ->test(static function($assert, $pair) {
            [$accepted, $set] = $pair;
            $values = \iterator_to_array($set->take(100)->enumerate());

            foreach ($values as $value) {
                $assert
                    ->array($accepted)
                    ->contains($value);
            }
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Set->exclude()',
            static function($assert) {
                $odds = Set::integers()
                    ->above(0)
                    ->exclude(static fn($i) => $i % 2 === 0)
                    ->take(100)
                    ->enumerate();

                foreach ($odds as $i) {
                    $assert->same(1, $i % 2);
                }
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Set::via()')
        ->given(
            $anySet,
            $anySet,
        )
        ->test(static fn($assert, $in, $out) => $assert->same(
            $out,
            $in->via(static function($set) use ($assert, $in, $out) {
                $assert->same($in, $test);

                return $out;
            }),
        ));

    yield $prove
        ->proof('Set->disableShrinking()')
        ->given($anySet)
        ->test(static function($assert, $set) {
            $set = $set
                ->disableShrinking()
                ->take(100);

            foreach ($set->values(Random::default) as $value) {
                $assert->null($value->shrink());
            }
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Set->zip()',
            static function($assert) {
                $set = Set::integers()
                    ->toSet()
                    ->zip(Set::integers())
                    ->take(100);

                foreach ($set->enumerate() as [$left, $right]) {
                    $assert
                        ->number($left)
                        ->int();
                    $assert
                        ->number($right)
                        ->int();
                }

                $value = $set
                    ->values(Random::default)
                    ->current();

                while ($shrunk = $value->shrink()) {
                    $value = $shrunk->a();
                }

                $assert->same([0, 0], $value->unwrap());
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Filtered out seed throws EmptySet')
        ->given($anySet)
        ->test(static function($assert, $set) {
            $assert->throws(
                static fn() => $set
                    ->flatMap(
                        static fn($seed) => $seed
                            ->toSet()
                            ->filter(static fn() => false),
                    )
                    ->enumerate()
                    ->current(),
                EmptySet::class,
            );
        })
        ->tag(Tag::ci, Tag::local, Tag::wip);
};
