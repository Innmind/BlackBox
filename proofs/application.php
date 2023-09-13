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
                ->tryToProve(Load::file(__DIR__.'/fixtures.php'));

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
            $assert
                ->expected('-1')
                ->not()
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
};
