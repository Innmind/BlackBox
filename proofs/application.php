<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Application,
    Random,
    Set,
    Runner\Load,
    Runner\IO\Collect,
    Runner\Printer\Standard,
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
    yield proof(
        'BlackBox can run with any of the random strategies',
        given(Set\Elements::of(...Random::cases())),
        static function($assert, $random) {
            $io = Collect::new();

            $result = Application::new([])
                ->useRandom($random)
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->allowProofsToNotMakeAnyAssertions()
                ->tryToProve(Load::file(__DIR__.'/../fixtures/proofs.php'));

            $assert->true($result->successful());
        },
    )->tag(Tag::ci, Tag::local);
    yield proof(
        'BlackBox can run with a specified number of scenarii per proof',
        given(Set\Integers::between(1, 10_000)), // limit to 10k so it doesn't take too mush time
        static function($assert, $scenarii) {
            $io = Collect::new();

            $result = Application::new([])
                ->scenariiPerProof($scenarii)
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->tryToProve(static function() {
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->true(true),
                    );
                });

            $assert->true($result->successful());
            $assert
                ->string($io->toString())
                ->contains("Scenarii: $scenarii");
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'BlackBox can run with a specified number of scenarii per property',
        given(Set\Integers::between(1, 50)), // limit to 50 so it doesn't take too much time
        static function($assert, $scenarii) {
            $io = Collect::new();

            $result = Application::new([])
                ->scenariiPerProof($scenarii)
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->tryToProve(static function() {
                    yield property(
                        LowerBoundAtZero::class,
                        Set\Elements::of(new Counter),
                    );
                });

            $assert->true($result->successful());
            $assert
                ->string($io->toString())
                ->contains("Scenarii: $scenarii");
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'BlackBox can run with a specified number of scenarii per properties',
        given(Set\Integers::between(1, 50)), // limit to 50 so it doesn't take too much time
        static function($assert, $scenarii) {
            $io = Collect::new();

            $result = Application::new([])
                ->scenariiPerProof($scenarii)
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->tryToProve(static function() {
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
                    );
                });

            $assert->true($result->successful());
            $assert
                ->string($io->toString())
                ->contains("Scenarii: $scenarii");
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'BlackBox can shrink the values of the proofs by default',
        static function($assert) {
            $io = Collect::new();

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->tryToProve(static function() {
                    yield proof(
                        'example',
                        given(
                            Set\Integers::any(),
                            Set\Integers::any(),
                        ),
                        static fn($assert, $a, $b) => $assert->true(false),
                    );
                });

            $assert->false($result->successful());
            $assert
                ->string($io->toString())
                ->contains('Failures: 1')
                ->contains('SF') // at least one shrinking
                ->contains('$a = 0') // as it is always the smallest value
                ->contains('$b = 0'); // as it is always the smallest value
        },
    )->tag(Tag::ci, Tag::local);
    yield test(
        'BlackBox can disable the shrinking mechanism',
        static function($assert) {
            $io = Collect::new();
            $value = null;

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->disableShrinking()
                ->tryToProve(static function() use (&$value) {
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static function($assert, $i) use (&$value) {
                            $value = $i;

                            $assert->true(false);
                        },
                    );
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
    )->tag(Tag::ci, Tag::local);

    yield test(
        'BlackBox can disable the memory limit',
        static function($assert) {
            $io = Collect::new();

            $assert
                ->expected('-1')
                ->not()
                ->same(\ini_get('memory_limit'));

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->disableMemoryLimit()
                ->tryToProve(static function() {
                    yield test(
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
    )->tag(Tag::ci, Tag::local);

    yield test(
        'BlackBox can stop on first failure',
        static function($assert) {
            $io = Collect::new();

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->stopOnFailure()
                ->tryToProve(static function() use (&$value) {
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->number($i),
                    );
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->true(false),
                    );
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->number($i),
                    );
                });

            $assert->false($result->successful());
            $assert
                ->string($io->toString())
                ->contains('Proofs: 2,');
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Application::map()',
        static function($assert) {
            $io = Collect::new();

            $result = Application::new([])
                ->map(
                    static fn($app) => $app
                        ->displayOutputVia($io)
                        ->displayErrorVia($io)
                        ->usePrinter(Standard::withoutColors())
                        ->stopOnFailure(),
                )
                ->tryToProve(static function() use (&$value) {
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->number($i),
                    );
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->true(false),
                    );
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->number($i),
                    );
                });

            $assert->false($result->successful());
            $assert
                ->string($io->toString())
                ->contains('Proofs: 2,');
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Application::when() enabled',
        static function($assert) {
            $io = Collect::new();

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->when(
                    true,
                    static fn($app) => $app
                        ->stopOnFailure(),
                )
                ->tryToProve(static function() use (&$value) {
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->number($i),
                    );
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->true(false),
                    );
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->number($i),
                    );
                });

            $assert->false($result->successful());
            $assert
                ->string($io->toString())
                ->contains('Proofs: 2,');
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Application::when() disabled',
        static function($assert) {
            $io = Collect::new();

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->when(
                    false,
                    static fn($app) => $app
                        ->stopOnFailure(),
                )
                ->tryToProve(static function() use (&$value) {
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->number($i),
                    );
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->true(false),
                    );
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => $assert->number($i),
                    );
                });

            $assert->false($result->successful());
            $assert
                ->string($io->toString())
                ->contains('Proofs: 3,');
        },
    )->tag(Tag::ci, Tag::local);

    yield test(
        'Application::allowProofsToNotMakeAnyAssertions()',
        static function($assert) {
            $io = Collect::new();

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->tryToProve(static function() use (&$value) {
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => null,
                    );
                });

            $assert->false($result->successful());
            $assert
                ->string($io->toString())
                ->contains('The proof did not make any assertion');

            $io = Collect::new();

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->allowProofsToNotMakeAnyAssertions()
                ->tryToProve(static function() use (&$value) {
                    yield proof(
                        'example',
                        given(Set\Integers::any()),
                        static fn($assert, $i) => null,
                    );
                });

            $assert->true($result->successful());
            $assert
                ->string($io->toString())
                ->not()
                ->contains('The proof did not make any assertion');
        },
    )->tag(Tag::ci, Tag::local);
};
