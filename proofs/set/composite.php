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
        'Test reduce Composite to the minimum values',
        static function($assert) {
            $io = Collect::new();

            $result = Application::new([])
                ->displayOutputVia($io)
                ->displayErrorVia($io)
                ->usePrinter(Standard::withoutColors())
                ->tryToProve(static function() {
                    yield proof(
                        'must not contain an "a"',
                        given(Set\Composite::immutable(
                            static fn($a, $b, $c) => [$a, $b, $c],
                            Set\Integers::any(),
                            Set\Strings::atLeast(1)->filter(
                                static fn($value) => \str_contains($value, 'a'),
                            ),
                            Set\Integers::any(),
                            Set\Integers::any(),
                        )),
                        static function($assert, $composite) {
                            $assert->string($composite[1])->not()->contains('a');
                        },
                    );
                });

            $assert->false($result->successful());
            $assert
                ->string($io->toString())
                ->contains(<<<EXPECTED
                \$composite = array:3 [
                  0 => 0
                  1 => "a"
                  2 => 0
                ]
                EXPECTED);
        },
    )->tag(Tag::ci, Tag::local);
};
