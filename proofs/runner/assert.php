<?php
declare(strict_types = 1);
declare(ticks = 1);

use Innmind\BlackBox\{
    Runner\Assert,
    Runner\Assert\Failure,
    Runner\Assert\Debug,
    Runner\Stats,
    Set,
    Tag,
};

return static function($load, $prove) {
    yield $prove
        ->proof('Assert->fail()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            try {
                $sut->fail($message);
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(0)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->that()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            try {
                $sut->that(static fn() => true);
            } catch (Throwable $e) {
                $assert->fail('it should not throw');
            }

            try {
                $sut->that(static fn() => false, $message);
                $assert->fail('it should throw');
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->throws()')
        ->given(
            Set::of(RuntimeException::class, LogicException::class, DomainException::class),
            Set::strings(),
        )
        ->test(static function($assert, $kind, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->throws(
                static fn() => throw new $kind($message),
                $kind,
            );

            try {
                $sut->throws(static fn() => null);
            } catch (Failure $e) {
                $assert
                    ->expected('Failed asserting that a callable throws an exception')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->not()->throws()')
        ->given(
            Set::of(RuntimeException::class, LogicException::class, DomainException::class),
            Set::strings(),
        )
        ->test(static function($assert, $kind, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->not()->throws(
                static fn() => null,
                $kind,
            );

            try {
                $sut->not()->throws(static fn() => throw new $kind($message));
            } catch (Failure $e) {
                $assert
                    ->expected('Failed asserting that a callable does not throw an exception')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->count()')
        ->given(
            Set::sequence(Set::type()),
            Set::integers()->above(0),
            Set::strings(),
        )
        ->exclude(static fn($values, $count) => \count($values) === $count)
        ->test(static function($assert, $values, $count, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->count(\count($values), $values);
            $sut->count(\count($values), new ArrayObject($values));

            try {
                $sut->count($count, $values);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert that a collection contains');
            }

            $assert
                ->expected(3)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->not()->count()')
        ->given(
            Set::sequence(Set::type()),
            Set::integers()->above(0),
            Set::strings(),
        )
        ->exclude(static fn($values, $count) => \count($values) === $count)
        ->test(static function($assert, $values, $count, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->not()->count($count, $values);
            $sut->not()->count($count, new ArrayObject($values));

            try {
                $sut->not()->count(\count($values), $values);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert that a collection does not contain');
            }

            $assert
                ->expected(3)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->true()')
        ->given(
            Set::type()->filter(static fn($value) => $value !== true),
            Set::strings(),
        )
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->true(true);

            try {
                $sut->true($value);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is true')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->not()->true()')
        ->given(
            Set::type()->filter(static fn($value) => $value !== true),
            Set::strings(),
        )
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->not()->true($value);

            try {
                $sut->not()->true(true);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is not true')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->false()')
        ->given(
            Set::type()->filter(static fn($value) => $value !== false),
            Set::strings(),
        )
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->false(false);

            try {
                $sut->false($value);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is false')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->not()->false()')
        ->given(
            Set::type()->filter(static fn($value) => $value !== false),
            Set::strings(),
        )
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->not()->false($value);

            try {
                $sut->not()->false(false);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is not false')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->bool()')
        ->given(
            Set::type()->filter(static fn($value) => !\is_bool($value)),
            Set::strings(),
        )
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->bool(true);
            $sut->bool(false);

            try {
                $sut->bool($value);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is a boolean')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(3)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->not()->bool()')
        ->given(
            Set::of(true, false),
            Set::type()->filter(static fn($value) => !\is_bool($value)),
            Set::strings(),
        )
        ->test(static function($assert, $bool, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->not()->bool($value);

            try {
                $sut->not()->bool($bool);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is not a boolean')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->null()')
        ->given(
            Set::type()->filter(static fn($value) => !\is_null($value)),
            Set::strings(),
        )
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->null(null);

            try {
                $sut->null($value);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is null')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->not()->null()')
        ->given(
            Set::type()->filter(static fn($value) => !\is_null($value)),
            Set::strings(),
        )
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->not()->null($value);

            try {
                $sut->not()->null(null);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is not null')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->resource()')
        ->given(
            Set::type()->filter(static fn($value) => !\is_resource($value)),
            Set::strings(),
        )
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->resource(\tmpfile());

            try {
                $sut->resource($value);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is a resource')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->expected()->same()')
        ->given(
            Set::type(),
            Set::type(),
            Set::strings(),
        )
        ->filter(static fn($a, $b) => $a !== $b)
        ->test(static function($assert, $a, $b, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->expected($a)->same($a);

            try {
                $sut->expected($a)->same($b);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert two variables are the same')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->expected()->not()->same()')
        ->given(
            Set::type(),
            Set::type(),
            Set::strings(),
        )
        ->filter(static fn($a, $b) => $a !== $b)
        ->test(static function($assert, $a, $b, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->expected($a)->not()->same($b);

            try {
                $sut->expected($a)->not()->same($a);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert two variables are not the same')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->expected()->equals()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->expected(42)->equals('42');

            try {
                $sut->expected(42)->equals(41);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert two variables are equal')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->expected()->not()->equals()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->expected(42)->not()->equals(41);

            try {
                $sut->expected(42)->not()->equals('42');
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert two variables are not equal')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->expected()->in()')
        ->given(
            Set::sequence(Set::type()),
            Set::type(),
            Set::sequence(Set::type()),
            Set::strings(),
        )
        ->filter(static fn($prefix, $value, $suffix) => !\in_array($value, $prefix, true) && !\in_array($value, $suffix, true))
        ->test(static function($assert, $prefix, $value, $suffix, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->expected($value)->in([...$prefix, $value, ...$suffix]);

            try {
                $sut->expected($value)->in([...$prefix, ...$suffix]);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is contained in an iterable')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->expected()->not()->in()')
        ->given(
            Set::sequence(Set::type()),
            Set::type(),
            Set::sequence(Set::type()),
            Set::strings(),
        )
        ->filter(static fn($prefix, $value, $suffix) => !\in_array($value, $prefix, true) && !\in_array($value, $suffix, true))
        ->test(static function($assert, $prefix, $value, $suffix, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->expected($value)->not()->in([...$prefix, ...$suffix]);

            try {
                $sut->expected($value)->not()->in([...$prefix, $value, ...$suffix]);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is not contained in an iterable')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->object()')
        ->given(
            Set::type(),
            Set::strings(),
        )
        ->filter(static fn($value) => !\is_object($value))
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->object(new class {
            });

            try {
                $sut->object($value);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is an object')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->object()->instance()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->object(new stdClass)->instance(stdClass::class);
            $sut->object(new LogicException)->instance(LogicException::class);

            try {
                $sut->object(new stdClass)->instance(LogicException::class);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert an object is an instance of');
            }

            $assert
                ->expected(6)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->object()->not()->instance()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->object(new stdClass)->not()->instance(LogicException::class);

            try {
                $sut->object(new stdClass)->not()->instance(stdClass::class);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert an object is not an instance of');
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->number()')
        ->given(
            Set::of(new stdClass, null, true, false, \tmpfile(), []),
            Set::either(
                Set::integers(),
                Set::realNumbers(),
            ),
            Set::strings(),
        )
        ->test(static function($assert, $value, $number, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->number($number);

            try {
                $sut->number($value);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is a number')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->number()->int()')
        ->given(
            Set::integers(),
            Set::strings(),
        )
        ->test(static function($assert, $int, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->number($int)->int();

            try {
                $sut->number(1.2)->int();
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a number is an integer')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->number()->float()')
        ->given(
            Set::integers(),
            Set::compose(
                static fn($int, $fraction) => $int * $fraction,
                Set::integers(),
                Set::of(0.1, 0.2, 0.01),
            ),
            Set::strings(),
        )
        ->test(static function($assert, $int, $float, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->number($float)->float();

            try {
                $sut->number($int)->float();
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a number is a float')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->number()->greaterThan()')
        ->given(
            Set::integers(),
            Set::integers()->above(1),
            Set::strings(),
        )
        ->test(static function($assert, $int, $diff, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->number($int)->greaterThan($int - $diff);

            try {
                $sut->number($int)->greaterThan($int);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a number is greater than another one')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->number()->greaterThanOrEqual()')
        ->given(
            Set::integers(),
            Set::integers()->above(0),
            Set::strings(),
        )
        ->test(static function($assert, $int, $diff, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->number($int)->greaterThanOrEqual($int - $diff);

            try {
                $sut->number($int)->greaterThanOrEqual($int + 1);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a number is greater than or equal to another one')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->number()->lessThan()')
        ->given(
            Set::integers(),
            Set::integers()->above(1),
            Set::strings(),
        )
        ->test(static function($assert, $int, $diff, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->number($int)->lessThan($int + $diff);

            try {
                $sut->number($int)->lessThan($int);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a number is less than another one')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->number()->lessThanOrEqual()')
        ->given(
            Set::integers(),
            Set::integers()->above(0),
            Set::strings(),
        )
        ->test(static function($assert, $int, $diff, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->number($int)->lessThanOrEqual($int + $diff);

            try {
                $sut->number($int)->lessThanOrEqual($int - 1);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a number is less than or equal to another one')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()')
        ->given(
            Set::strings(),
            Set::of([], true, null, new ArrayObject),
            Set::strings(),
        )
        ->test(static function($assert, $string, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string($string);

            try {
                $sut->string($value);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is a string')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->empty()')
        ->given(
            Set::strings()->atLeast(1),
            Set::strings(),
        )
        ->test(static function($assert, $string, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string('')->empty();

            try {
                $sut->string($string)->empty();
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a string is empty')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->not()->empty()')
        ->given(
            Set::strings()->atLeast(1),
            Set::strings(),
        )
        ->test(static function($assert, $string, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string($string)->not()->empty();

            try {
                $sut->string('')->not()->empty();
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a string is not empty')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->contains()')
        ->given(
            Set::strings(),
            Set::strings()->atLeast(1),
            Set::strings(),
            Set::strings(),
        )
        ->filter(static fn($prefix, $string, $suffix) => !\str_contains($prefix, $string) && !\str_contains($suffix, $string))
        ->test(static function($assert, $prefix, $string, $suffix, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string($prefix.$string.$suffix)->contains($string);

            try {
                $sut->string($prefix.$suffix)->contains($string);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert a string contains');
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->not()->contains()')
        ->given(
            Set::strings(),
            Set::strings()->atLeast(1),
            Set::strings(),
            Set::strings(),
        )
        ->filter(static fn($prefix, $string, $suffix) => !\str_contains($prefix, $string) && !\str_contains($suffix, $string))
        ->test(static function($assert, $prefix, $string, $suffix, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string($prefix.$suffix)->not()->contains($string);

            try {
                $sut->string($prefix.$string.$suffix)->not()->contains($string);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert a string does not contain');
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->matches()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string('2023-05-24')->matches('~^\d{4}-\d{2}-\d{2}$~');

            try {
                $sut->string('2023-05-24')->matches('~^\d{2}-\d{2}-\d{2}$~');
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert a string matches the pattern');
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->not()->matches()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string('2023-05-24')->not()->matches('~^\d{2}-\d{2}-\d{2}$~');

            try {
                $sut->string('2023-05-24')->not()->matches('~^\d{4}-\d{2}-\d{2}$~');
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert a string does not match the pattern');
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->startsWith()')
        ->given(
            Set::strings()->atLeast(1),
            Set::strings(),
            Set::strings(),
        )
        ->filter(static fn($string, $suffix) => !\str_contains($suffix, $string))
        ->test(static function($assert, $string, $suffix, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string($string.$suffix)->startsWith($string);

            try {
                $sut->string($suffix)->startsWith($string);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert a string starts with');
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->not()->startsWith()')
        ->given(
            Set::strings()->atLeast(1),
            Set::strings(),
            Set::strings(),
        )
        ->filter(static fn($string, $suffix) => !\str_contains($suffix, $string))
        ->test(static function($assert, $string, $suffix, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string($suffix)->not()->startsWith($string);

            try {
                $sut->string($string.$suffix)->not()->startsWith($string);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert a string does not start with');
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->endsWith()')
        ->given(
            Set::strings(),
            Set::strings()->atLeast(1),
            Set::strings(),
        )
        ->filter(static fn($prefix, $string) => !\str_contains($prefix, $string))
        ->test(static function($assert, $prefix, $string, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string($prefix.$string)->endsWith($string);

            try {
                $sut->string($prefix)->endsWith($string);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert a string ends with');
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->string()->not()->endsWith()')
        ->given(
            Set::strings(),
            Set::strings()->atLeast(1),
            Set::strings(),
        )
        ->filter(static fn($prefix, $string) => !\str_contains($prefix, $string))
        ->test(static function($assert, $prefix, $string, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->string($prefix)->not()->endsWith($string);

            try {
                $sut->string($prefix.$string)->not()->endsWith($string);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert a string does not end with');
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->array()')
        ->given(
            Set::sequence(Set::type()),
            Set::of('', true, null, new ArrayObject),
            Set::strings(),
        )
        ->test(static function($assert, $array, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->array($array);

            try {
                $sut->array($value);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->expected('Failed to assert a variable is an array')
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->array()->hasKey')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->array([42])->hasKey(0);
            $sut->array(['foo' => 42])->hasKey('foo');

            try {
                $sut->array([])->hasKey(0);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert an array has the key');
            }

            $assert
                ->expected(6)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->array()->not()->hasKey')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut->array([42])->not()->hasKey(1);
            $sut->array(['foo' => 42])->not()->hasKey('bar');

            try {
                $sut->array([1])->not()->hasKey(0);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert
                    ->string($e->kind()->message())
                    ->startsWith('Failed to assert an array does not have the key');
            }

            $assert
                ->expected(6)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->array()->contains()')
        ->given(
            Set::type(),
            Set::sequence(Set::type()),
            Set::sequence(Set::type()),
            Set::strings(),
        )
        ->filter(
            static fn($expected, $prefix, $suffix) => !\in_array($expected, $prefix, true) &&
                !\in_array($expected, $suffix, true),
        )
        ->test(static function($assert, $expected, $prefix, $suffix, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $array = [...$prefix, ...[$expected], ...$suffix];

            $sut->array($array)->contains($expected);

            try {
                $sut->array([...$prefix, ...$suffix])->contains($expected);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert->same(
                    'Failed to assert an array contains a value',
                    $e->kind()->message(),
                );
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->array()->not()->contains()')
        ->given(
            Set::type(),
            Set::sequence(Set::type()),
            Set::sequence(Set::type()),
            Set::strings(),
        )
        ->filter(
            static fn($expected, $prefix, $suffix) => !\in_array($expected, $prefix, true) &&
                !\in_array($expected, $suffix, true),
        )
        ->test(static function($assert, $expected, $prefix, $suffix, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $array = [...$prefix, ...[$expected], ...$suffix];

            $sut->array([...$prefix, ...$suffix])->not()->contains($expected);

            try {
                $sut->array($array)->not()->contains($expected);
                $assert->fail($message);
            } catch (Failure $e) {
                $assert
                    ->expected($message)
                    ->not()
                    ->same($e->kind()->message());
                $assert->same(
                    'Failed to assert an array does not contain a value',
                    $e->kind()->message(),
                );
            }

            $assert
                ->expected(4)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->matches()')
        ->given(
            Set::type(),
            Set::strings(),
        )
        ->test(static function($assert, $value, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $assert->same(
                $value,
                $sut->matches(static function($sut) use ($value) {
                    $sut->true(true);

                    return $value;
                }),
            );

            try {
                $sut->matches(static fn($sut) => $sut->fail($message));
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(1)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    // For now time assertions are not verified inside the CI because it doesn't
    // seem to respect the usleep
    yield $prove
        ->proof('Assert->time()->inLessThan()->milliseconds()')
        ->given(
            Set::integers()->between(0, 800),
            Set::strings(),
        )
        ->test(static function($assert, $microseconds, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->time(static fn() => \usleep($microseconds))
                ->inLessThan()
                ->milliseconds(1);

            try {
                $sut
                    ->time(static fn() => \usleep($microseconds + 2_000))
                    ->inLessThan()
                    ->milliseconds(1, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->take(10)
        ->tag(Tag::local);

    yield $prove
        ->proof('Assert->time()->inLessThan()->seconds()')
        ->given(
            Set::integers()->between(0, 800_000),
            Set::strings(),
        )
        ->test(static function($assert, $microseconds, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->time(static fn() => \usleep($microseconds))
                ->inLessThan()
                ->seconds(1);

            try {
                $sut
                    ->time(static fn() => \usleep($microseconds + 2_000_000))
                    ->inLessThan()
                    ->seconds(1, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->take(10)
        ->tag(Tag::local);

    yield $prove
        ->proof('Assert->time()->inMoreThan()->milliseconds()')
        ->given(
            Set::integers()->between(1, 800), // no need to go higher
            Set::strings(),
        )
        ->test(static function($assert, $microseconds, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->time(static fn() => \usleep($microseconds + 1_000))
                ->inMoreThan()
                ->milliseconds(1);

            try {
                $sut
                    ->time(static fn() => \usleep($microseconds))
                    ->inMoreThan()
                    ->milliseconds(2, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->take(10)
        ->tag(Tag::local);

    yield $prove
        ->proof('Assert->time()->inMoreThan()->seconds()')
        ->given(
            Set::integers()->between(1, 800_000), // no need to go higher
            Set::strings(),
        )
        ->test(static function($assert, $microseconds, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->time(static fn() => \usleep($microseconds + 1_000_000))
                ->inMoreThan()
                ->seconds(1);

            try {
                $sut
                    ->time(static fn() => \usleep($microseconds))
                    ->inMoreThan()
                    ->seconds(2, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->take(10)
        ->tag(Tag::local);

    yield $prove
        ->proof('Assert->memory()->inLessThan()->bytes()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->memory(static fn() => null)
                ->inLessThan()
                ->bytes(1);

            try {
                $sut
                    ->memory(static function() {
                        $foo = \str_repeat('a', 2);
                    })
                    ->inLessThan()
                    ->bytes(1, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->memory()->inLessThan()->kiloBytes()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->memory(static fn() => null)
                ->inLessThan()
                ->kiloBytes(1);

            try {
                $sut
                    ->memory(static function() {
                        $foo = \str_repeat('a', 1_100);
                    })
                    ->inLessThan()
                    ->kiloBytes(1, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->memory()->inLessThan()->megaBytes()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->memory(static fn() => null)
                ->inLessThan()
                ->megaBytes(1);

            try {
                $sut
                    ->memory(static function() {
                        $foo = \str_repeat('a', 1_100_000);
                    })
                    ->inLessThan()
                    ->megaBytes(1, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->memory()->inMoreThan()->bytes()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->memory(static function() {
                    $foo = \str_repeat('a', 2);
                })
                ->inMoreThan()
                ->bytes(1);

            try {
                $sut
                    ->memory(static fn() => null)
                    ->inMoreThan()
                    ->bytes(1, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->memory()->inMoreThan()->kiloBytes()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->memory(static function() {
                    $foo = \str_repeat('a', 1_100);
                })
                ->inMoreThan()
                ->kiloBytes(1);

            try {
                $sut
                    ->memory(static fn() => null)
                    ->inMoreThan()
                    ->kiloBytes(1, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);

    yield $prove
        ->proof('Assert->memory()->inMoreThan()->megaBytes()')
        ->given(Set::strings())
        ->test(static function($assert, $message) {
            $sut = Assert::of(
                $stats = Stats::new(),
                Debug::new(),
            );

            $sut
                ->memory(static function() {
                    $foo = \str_repeat('a', 1_100_000);
                })
                ->inMoreThan()
                ->megaBytes(1);

            try {
                $sut
                    ->memory(static fn() => null)
                    ->inMoreThan()
                    ->megaBytes(1, $message);
                $assert->fail('it should throw');
            } catch (Throwable $e) {
                $assert
                    ->object($e)
                    ->instance(Failure::class);
                $assert
                    ->expected($message)
                    ->same($e->kind()->message());
            }

            $assert
                ->expected(2)
                ->same($stats->assertions());
        })
        ->tag(Tag::ci, Tag::local);
};
