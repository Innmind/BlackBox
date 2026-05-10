<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Application,
    Random,
    Set,
    Runner\Load,
    Runner\IO\Collect,
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

return static function($prove) {
    yield $prove
        ->proof('BlackBox can run with any of the random strategies')
        ->given(Set::of(...Random::cases()))
        ->test(static function($assert, $random) {
            $result = Application::new([])
                ->useRandom($random)
                ->displayVia(Collect::new())
                ->allowProofsToNotMakeAnyAssertions()
                ->tryToProve(Load::file(__DIR__.'/../fixtures/proofs.php'));

            $assert->true($result->successful());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('BlackBox can run with a specified number of scenarii per proof')
        ->given(Set::integers()->between(1, 10_000)) // limit to 10k so it doesn't take too mush time
        ->test(static function($assert, $scenarii) {
            $io = Collect::new();

            $result = Application::new([])
                ->scenariiPerProof($scenarii)
                ->displayVia($io)
                ->tryToProve(static function($prove) {
                    yield $prove
                        ->proof('example')
                        ->given(Set::integers())
                        ->test(static fn($assert, $i) => $assert->true(true));
                });

            $assert->true($result->successful());
            $assert
                ->string($io->toString())
                ->contains("Scenarii: $scenarii");
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('BlackBox can run with a specified number of scenarii per property')
        ->given(Set::integers()->between(1, 50)) // limit to 50 so it doesn't take too much time
        ->test(static function($assert, $scenarii) {
            $io = Collect::new();

            $result = Application::new([])
                ->scenariiPerProof($scenarii)
                ->displayVia($io)
                ->tryToProve(static function($prove) {
                    yield $prove->property(
                        LowerBoundAtZero::class,
                        Set::of(static fn() => new Counter),
                    );
                });

            $assert->true($result->successful());
            $assert
                ->string($io->toString())
                ->contains("Scenarii: $scenarii");
        })->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('BlackBox can run with a specified number of scenarii per properties')
        ->given(Set::integers()->between(1, 50)) // limit to 50 so it doesn't take too much time
        ->test(static function($assert, $scenarii) {
            $io = Collect::new();

            $result = Application::new([])
                ->scenariiPerProof($scenarii)
                ->displayVia($io)
                ->allowProofsToNotMakeAnyAssertions()
                ->tryToProve(static function($prove) {
                    yield $prove->properties(
                        'Counter properties',
                        Set::properties(
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
                    );
                });

            $assert->true($result->successful());
            $assert
                ->string($io->toString())
                ->contains("Scenarii: $scenarii");
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'BlackBox can shrink the values of the proofs by default',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->tryToProve(static function($prove) {
                        yield $prove
                            ->proof('example')
                            ->given(
                                Set::integers(),
                                Set::integers(),
                            )
                            ->test(static fn($assert, $a, $b) => $assert->true(false));
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('Failures: 1')
                    ->contains('SF') // at least one shrinking
                    ->contains('$a = 0') // as it is always the smallest value
                    ->contains('$b = 0'); // as it is always the smallest value
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'BlackBox can disable the shrinking mechanism',
            static function($assert) {
                $io = Collect::new();
                $value = null;

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->disableShrinking()
                    ->tryToProve(static function($prove) use (&$value) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static function($assert, $i) use (&$value) {
                                $value = $i;

                                $assert->true(false);
                            });
                    });

                $assert->false($result->successful());
                $assert->number($value); // to make sure run at least once
                $assert
                    ->string($io->toString())
                    ->contains('Failures: 1')
                    ->contains('F')
                    ->contains("\$i = $value")
                    ->not()
                    ->contains('SF', 'The shrinking has not been disabled');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'BlackBox can disable the memory limit',
            static function($assert) {
                $assert
                    ->expected('-1')
                    ->not()
                    ->same(\ini_get('memory_limit'));

                $result = Application::new([])
                    ->displayVia(Collect::new())
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->disableMemoryLimit()
                    ->tryToProve(static function($prove) {
                        yield $prove->test(
                            'example',
                            static fn($assert) => $assert->same(
                                '-1',
                                \ini_get('memory_limit'),
                            ),
                        );
                    });

                $assert->true($result->successful());
                // It seems PHP prevents setting the limit lower to the peak memory
                // usage so we can't reset the limit to the previous value
                $assert
                    ->expected('-1')
                    ->same(\ini_get('memory_limit'));
            },
        )
        ->tag(Tag::ci);

    yield $prove
        ->test(
            'BlackBox can stop on first failure',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->stopOnFailure()
                    ->tryToProve(static function($prove) use (&$value) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->number($i));

                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->true(false));

                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->number($i));
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('Proofs: 2,');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Application::map()',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->map(
                        static fn($app) => $app
                            ->displayVia($io)
                            ->mapPrinter(static fn($printer) => $printer->withoutColors())
                            ->stopOnFailure(),
                    )
                    ->tryToProve(static function($prove) use (&$value) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->number($i));

                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->true(false));

                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->number($i));
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('Proofs: 2,');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Application::when() enabled',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->when(
                        true,
                        static fn($app) => $app
                            ->stopOnFailure(),
                    )
                    ->tryToProve(static function($prove) use (&$value) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->number($i));

                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->true(false));

                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->number($i));
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('Proofs: 2,');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Application::when() disabled',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->when(
                        false,
                        static fn($app) => $app
                            ->stopOnFailure(),
                    )
                    ->tryToProve(static function($prove) use (&$value) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->number($i));

                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->true(false));

                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => $assert->number($i));
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('Proofs: 3,');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Application::allowProofsToNotMakeAnyAssertions()',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->tryToProve(static function($prove) use (&$value) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => null);
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('The proof did not make any assertion');

                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->allowProofsToNotMakeAnyAssertions()
                    ->tryToProve(static function($prove) use (&$value) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers())
                            ->test(static fn($assert, $i) => null);
                    });

                $assert->true($result->successful());
                $assert
                    ->string($io->toString())
                    ->not()
                    ->contains('The proof did not make any assertion');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'The application stops shrinking when the type of error changes',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->tryToProve(static function($prove) use (&$value) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers()->above(0))
                            ->test(static function($assert, $i) {
                                if ($i > 42) {
                                    $assert->true(false);
                                }

                                $assert->true(false);
                            });
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('$i = 43');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Application::useExhaustiveShrinking()',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->useExhaustiveShrinking()
                    ->tryToProve(static function($prove) use (&$value) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers()->above(0))
                            ->test(static function($assert, $i) {
                                if ($i > 42) {
                                    $assert->true(false);
                                }

                                $assert->true(false);
                            });
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('$i = 0');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Application::filterOnTags()',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->filterOnTags(Tag::local)
                    ->tryToProve(static function($prove) {
                        yield $prove
                            ->test(
                                'example',
                                static fn($assert) => $assert->true(true),
                            )
                            ->tag(Tag::local);

                        yield $prove
                            ->test(
                                'example',
                                static fn($assert) => $assert->true(true),
                            )
                            ->tag(Tag::ci);
                    });

                $assert->true($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('Proofs: 1, Scenarii: 1');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Debug variables are resetted between proofs',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->tryToProve(static function($prove) {
                        yield $prove->test(
                            'example',
                            static fn($assert) => $assert
                                ->debug('foo', true)
                                ->true(false),
                        );

                        yield $prove->test(
                            'example',
                            static fn($assert) => $assert
                                ->debug('foo', false)
                                ->true(false),
                        );
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('$foo = true')
                    ->contains('$foo = false');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Debug variables are resetted when shrinking',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->tryToProve(static function($prove) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers()->between(0, 100))
                            ->test(
                                static fn($assert, $i) => $assert
                                    ->debug('i'.$i, $i)
                                    ->true(false),
                            );
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('$i0 = 0')
                    ->not()
                    ->contains('$i1 = 1');
            },
        )
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->test(
            'Shrinking can be disabled on each proof',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->mapProof(static fn($proof) => match ($proof->tagged(Tag::positive)) {
                        true => $proof->disableShrinking(),
                        false => $proof,
                    })
                    ->tryToProve(static function($prove) {
                        yield $prove
                            ->proof('example')
                            ->given(Set::integers()->between(1, 100))
                            ->test(
                                static fn($assert, $a) => $assert->same(0, $a),
                            )
                            ->tag(Tag::positive);

                        yield $prove
                            ->proof('example')
                            ->given(Set::integers()->between(1, 100))
                            ->test(
                                static fn($assert, $b) => $assert->same(0, $b),
                            )
                            ->tag(Tag::negative);
                    });

                $assert->false($result->successful());
                $assert
                    ->string($io->toString())
                    ->contains('$b = 1')
                    ->not()
                    ->contains('$a = 0')
                    ->contains("SF\n\n\$a = 1\n");
            },
        )
        ->tag(Tag::ci, Tag::local);
};
