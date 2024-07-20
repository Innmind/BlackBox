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
                        'must not contain an "a"',
                        given(
                            Set\Sequence::of(Set\Strings::any())->filter(
                                static function($values) {
                                    foreach ($values as $value) {
                                        if (\str_contains($value, 'a')) {
                                            return true;
                                        }
                                    }

                                    return false;
                                },
                            ),
                        ),
                        static function($assert, $values) {
                            foreach ($values as $value) {
                                $assert->string($value)->not()->contains('a');
                            }
                        },
                    );
                });

            $assert->false($result->successful());
            $assert
                ->string($io->toString())
                ->contains(<<<EXPECTED
                \$values = array:1 [
                  0 => "a"
                ]
                EXPECTED);
        },
    )->tag(Tag::ci, Tag::local);
};
