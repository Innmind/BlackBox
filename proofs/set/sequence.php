<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Application,
    Runner\IO\Collect,
    Set,
    Tag,
};
use Innmind\BlackBox\Runner\Printer\Standard;

return static function() {
    yield test(
        'Test reduce Sequence to the minimum values',
        static function($assert) {
            $io = Collect::new();

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->tryToProve(static function() {
                    yield proof(
                        'must not contain a "0"',
                        given(
                            Set\Sequence::of(Set\Integers::above(0))->atLeast(1),
                        ),
                        static function($assert, $values) {
                            foreach ($values as $value) {
                                $assert->same(0, $value);
                            }
                        },
                    );
                });

            $assert->false($result->successful());
            $assert
                ->string($io->toString())
                ->contains(<<<EXPECTED
                \$values = array:1 [
                  0 => 1
                ]
                EXPECTED);
        },
    )->tag(Tag::ci, Tag::local);
};
