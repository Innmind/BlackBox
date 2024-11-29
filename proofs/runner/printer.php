<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Runner\Printer\Standard,
    Runner\IO\Collect,
    Runner\Stats,
    Runner\Proof\Name,
    Runner\Proof\Scenario\Failure,
    Runner\Proof\Scenario,
    Runner\Assert,
    Runner\Assert\Failure\Truth,
    Runner\Assert\Failure\Property,
    Runner\Assert\Failure\Comparison,
    Set,
    Set\Value,
    Properties,
    Tag,
};
use Fixtures\Innmind\BlackBox\{
    Counter,
    LowerBoundAtZero,
};

return static function() {
    yield test(
        'Printer->start()',
        static function($assert) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer->start($io, $io);

            $assert->same(["BlackBox\n"], $io->written());
        },
    )->tag(Tag::ci, Tag::local);
    yield proof(
        'Printer->end() on success',
        given(
            Set\Integers::between(0, 10_000), // not above 10k to limit the time it takes
            Set\Integers::between(0, 10_000), // not above 10k to limit the time it takes
            Set\Integers::between(0, 10_000), // not above 10k to limit the time it takes
        ),
        static function($assert, $proofs, $scenarii, $assertions) {
            $printer = Standard::new();
            $io = Collect::new();
            $stats = Stats::new();

            for ($i = 0; $i < $proofs; ++$i) {
                $stats->incrementProofs();
            }

            for ($i = 0; $i < $scenarii; ++$i) {
                $stats->incrementScenarii();
            }

            for ($i = 0; $i < $assertions; ++$i) {
                $stats->incrementAssertions();
            }

            $printer->start($io, $io);
            $printer->end($io, $io, $stats);

            $written = $io->written();
            $assert->count(4, $written);
            $assert
                ->array($written)
                ->hasKey(0)
                ->hasKey(1)
                ->hasKey(2)
                ->hasKey(3);
            $assert
                ->string($written[1])
                ->matches('~^Time: \d{2}:\d{2}(\.\d{3})?, Memory: \d{1,3}\.\d{2} [KM]B$~');
            $assert->same("\n\n", $written[2]);
            $assert->same(
                "OK\nProofs: $proofs, Scenarii: $scenarii, Assertions: $assertions\n",
                $written[3],
            );
        },
    )->tag(Tag::ci, Tag::local);
    yield proof(
        'Printer->end() on failure',
        given(
            Set\Integers::between(0, 10_000), // not above 10k to limit the time it takes
            Set\Integers::between(0, 10_000), // not above 10k to limit the time it takes
            Set\Integers::between(0, 10_000), // not above 10k to limit the time it takes
            Set\Integers::between(1, 10_000), // not above 10k to limit the time it takes
        ),
        static function($assert, $proofs, $scenarii, $assertions, $failures) {
            $printer = Standard::new();
            $io = Collect::new();
            $stats = Stats::new();

            for ($i = 0; $i < $proofs; ++$i) {
                $stats->incrementProofs();
            }

            for ($i = 0; $i < $scenarii; ++$i) {
                $stats->incrementScenarii();
            }

            for ($i = 0; $i < $assertions; ++$i) {
                $stats->incrementAssertions();
            }

            for ($i = 0; $i < $failures; ++$i) {
                $stats->incrementFailures();
            }

            $printer->start($io, $io);
            $printer->end($io, $io, $stats);

            $written = $io->written();
            $assert->count(4, $written);
            $assert
                ->array($written)
                ->hasKey(0)
                ->hasKey(1)
                ->hasKey(2)
                ->hasKey(3);
            $assert
                ->string($written[1])
                ->matches('~^Time: \d{2}:\d{2}(\.\d{3})?, Memory: \d{1,3}\.\d{2} [KM]B$~');
            $assert->same("\n\n", $written[2]);
            $assert->same(
                "Failed\nProofs: $proofs, Scenarii: $scenarii, Assertions: $assertions, Failures: $failures\n",
                $written[3],
            );
        },
    )->tag(Tag::ci, Tag::local);
    yield proof(
        'Printer->proof()',
        given(
            Set\Strings::any(),
            Set\Sequence::of(Set\Elements::of(...Tag::cases())),
        ),
        static function($assert, $name, $tags) {
            $printer = Standard::new()->disableGitHubOutput();
            $io = Collect::new();

            $printer->proof($io, $io, Name::of($name), $tags);

            $written = $io->toString();

            foreach ($tags as $tag) {
                $assert
                    ->string($written)
                    ->contains($tag->name);
            }

            $assert
                ->string($written)
                ->contains($name);
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Printer->proof() in GitHub Action',
        given(
            Set\Strings::any(),
            Set\Sequence::of(Set\Elements::of(...Tag::cases())),
        ),
        static function($assert, $name, $tags) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer->proof($io, $io, Name::of($name), $tags);

            $written = $io->toString();

            foreach ($tags as $tag) {
                $assert
                    ->string($written)
                    ->contains($tag->name);
            }

            $assert
                ->string($written)
                ->startsWith('::group::')
                ->contains($name);
        },
    )->tag(Tag::ci);

    yield proof(
        'Printer->proof()->emptySet()',
        given(
            Set\Strings::any(),
            Set\Sequence::of(Set\Elements::of(...Tag::cases())),
        ),
        static function($assert, $name, $tags) {
            $printer = Standard::new()->disableGitHubOutput();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), $tags)
                ->emptySet($io, $io);

            $written = $io->written();

            $assert
                ->expected("No scenario found\n")
                ->same(\end($written));
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Printer->proof()->emptySet() in GitHub Action',
        given(
            Set\Strings::any(),
            Set\Sequence::of(Set\Elements::of(...Tag::cases())),
        ),
        static function($assert, $name, $tags) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), $tags)
                ->emptySet($io, $io);

            $written = \implode('', $io->written());

            $assert
                ->string($written)
                ->endsWith("No scenario found\n::endgroup::\n");
        },
    )->tag(Tag::ci);

    yield proof(
        'Printer->proof()->success()',
        given(
            Set\Strings::any(),
            Set\Sequence::of(Set\Elements::of(...Tag::cases())),
        ),
        static function($assert, $name, $tags) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), $tags)
                ->success($io, $io);

            $written = $io->written();

            $assert
                ->expected('.')
                ->same(\end($written));
        },
    )->tag(Tag::ci, Tag::local);
    yield proof(
        'Printer->proof()->shrunk()',
        given(
            Set\Strings::any(),
            Set\Sequence::of(Set\Elements::of(...Tag::cases())),
        ),
        static function($assert, $name, $tags) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), $tags)
                ->shrunk($io, $io);

            $written = $io->written();

            $assert
                ->expected('S')
                ->same(\end($written));
        },
    )->tag(Tag::ci, Tag::local);
    yield proof(
        'Printer->proof()->failure() for Failure\Truth',
        given(
            Set\Strings::any(),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::any(),
        ),
        static function($assert, $name, $val, $truth) {
            $printer = Standard::new()->disableGitHubOutput();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), [])
                ->failed($io, $io, Failure::of(
                    Assert\Failure::of(Truth::of($truth)),
                    Value::immutable(Scenario\Inline::of(
                        [$val],
                        static fn($assert, $foo) => null,
                    )),
                ));

            $written = $io->toString();

            $assert
                ->string($written)
                ->contains("F\n\n")
                ->contains('$foo = ')
                ->contains($val)
                ->contains($truth);
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Printer->proof()->failure() for Failure\Truth in GitHub Action',
        given(
            Set\Strings::any(),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::any(),
        ),
        static function($assert, $name, $val, $truth) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), [])
                ->failed($io, $io, Failure::of(
                    Assert\Failure::of(Truth::of($truth)),
                    Value::immutable(Scenario\Inline::of(
                        [$val],
                        static fn($assert, $foo) => null,
                    )),
                ));

            $written = $io->toString();

            $assert
                ->string($written)
                ->contains("F\n\n")
                ->contains('$foo = ')
                ->contains($val)
                ->contains('::error ::')
                ->contains($truth);
        },
    )->tag(Tag::ci);

    yield proof(
        'Printer->proof()->failure() for Failure\Property',
        given(
            Set\Strings::any(),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::any(),
        ),
        static function($assert, $name, $property, $val, $message) {
            $printer = Standard::new()->disableGitHubOutput();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), [])
                ->failed($io, $io, Failure::of(
                    Assert\Failure::of(Property::of(
                        $property,
                        $message,
                    )),
                    Value::immutable(Scenario\Inline::of(
                        [$val],
                        static fn($assert, $foo) => null,
                    )),
                ));

            $written = $io->toString();

            $assert
                ->string($written)
                ->contains("F\n\n")
                ->contains('$variable = ')
                ->contains($property)
                ->contains('$foo = ')
                ->contains($val)
                ->contains($message);
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Printer->proof()->failure() for Failure\Property in GitHub Action',
        given(
            Set\Strings::any(),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::any(),
        ),
        static function($assert, $name, $property, $val, $message) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), [])
                ->failed($io, $io, Failure::of(
                    Assert\Failure::of(Property::of(
                        $property,
                        $message,
                    )),
                    Value::immutable(Scenario\Inline::of(
                        [$val],
                        static fn($assert, $foo) => null,
                    )),
                ));

            $written = $io->toString();

            $assert
                ->string($written)
                ->contains("F\n\n")
                ->contains('$variable = ')
                ->contains($property)
                ->contains('$foo = ')
                ->contains($val)
                ->contains('::error ::')
                ->contains($message);
        },
    )->tag(Tag::ci);

    yield proof(
        'Printer->proof()->failure() for Failure\Comparison',
        given(
            Set\Strings::any(),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::any(),
        ),
        static function($assert, $name, $expected, $actual, $val, $message) {
            $printer = Standard::new()->disableGitHubOutput();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), [])
                ->failed($io, $io, Failure::of(
                    Assert\Failure::of(Comparison::of(
                        $expected,
                        $actual,
                        $message,
                    )),
                    Value::immutable(Scenario\Inline::of(
                        [$val],
                        static fn($assert, $foo) => null,
                    )),
                ));

            $written = $io->toString();

            $assert
                ->string($written)
                ->contains("F\n\n")
                ->contains('$expected = ')
                ->contains($expected)
                ->contains('$actual = ')
                ->contains($actual)
                ->contains('$foo = ')
                ->contains($val)
                ->contains($message);
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Printer->proof()->failure() for Failure\Comparison in GitHub Action',
        given(
            Set\Strings::any(),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::madeOf(Set\Chars::alphanumerical()),
            Set\Strings::any(),
        ),
        static function($assert, $name, $expected, $actual, $val, $message) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), [])
                ->failed($io, $io, Failure::of(
                    Assert\Failure::of(Comparison::of(
                        $expected,
                        $actual,
                        $message,
                    )),
                    Value::immutable(Scenario\Inline::of(
                        [$val],
                        static fn($assert, $foo) => null,
                    )),
                ));

            $written = $io->toString();

            $assert
                ->string($written)
                ->contains("F\n\n")
                ->contains('$expected = ')
                ->contains($expected)
                ->contains('$actual = ')
                ->contains($actual)
                ->contains('$foo = ')
                ->contains($val)
                ->contains('::error ::')
                ->contains($message);
        },
    )->tag(Tag::ci);

    yield proof(
        'Printer->proof()->failure() for Scenario\Property',
        given(
            Set\Strings::any(),
            Set\Strings::any(),
        ),
        static function($assert, $name, $message) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), [])
                ->failed($io, $io, Failure::of(
                    Assert\Failure::of(Truth::of($message)),
                    Value::immutable(Scenario\Property::of(
                        new LowerBoundAtZero,
                        new Counter,
                    )),
                ));

            $written = $io->toString();

            $assert
                ->string($written)
                ->contains("F\n\n")
                ->contains('$property = ')
                ->contains('Fixtures\Innmind\BlackBox\LowerBoundAtZero')
                ->contains('$systemUnderTest = ')
                ->contains('Fixtures\Innmind\BlackBox\Counter')
                ->contains($message);
        },
    )->tag(Tag::ci, Tag::local);
    yield proof(
        'Printer->proof()->failure() for Scenario\Properties',
        given(
            Set\Strings::any(),
            Set\Strings::any(),
        ),
        static function($assert, $name, $message) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), [])
                ->failed($io, $io, Failure::of(
                    Assert\Failure::of(Truth::of($message)),
                    Value::immutable(Scenario\Properties::of(
                        Properties::of(new LowerBoundAtZero),
                        new Counter,
                    )),
                ));

            $written = $io->toString();

            $assert
                ->string($written)
                ->contains("F\n\n")
                ->contains('$properties = ')
                ->contains('Fixtures\Innmind\BlackBox\LowerBoundAtZero')
                ->contains('$systemUnderTest = ')
                ->contains('Fixtures\Innmind\BlackBox\Counter')
                ->contains($message);
        },
    )->tag(Tag::ci, Tag::local);
    yield proof(
        'Printer->proof()->end()',
        given(
            Set\Strings::any(),
            Set\Sequence::of(Set\Elements::of(...Tag::cases())),
        ),
        static function($assert, $name, $tags) {
            $printer = Standard::new()->disableGitHubOutput();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), $tags)
                ->end($io, $io);

            $written = $io->written();

            $assert
                ->expected("\n\n")
                ->same(\end($written));
        },
    )->tag(Tag::ci, Tag::local);

    yield proof(
        'Printer->proof()->end() in GitHub Action',
        given(
            Set\Strings::any(),
            Set\Sequence::of(Set\Elements::of(...Tag::cases())),
        ),
        static function($assert, $name, $tags) {
            $printer = Standard::new();
            $io = Collect::new();

            $printer
                ->proof($io, $io, Name::of($name), $tags)
                ->end($io, $io);

            $written = \implode('', $io->written());

            $assert
                ->string($written)
                ->endsWith("\n\n::endgroup::\n");
        },
    )->tag(Tag::ci);
};
