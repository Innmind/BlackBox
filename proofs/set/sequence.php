<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Application,
    Runner\IO\Collect,
    Runner\Printer\Standard,
    Set,
    Tag,
};

return static function($load, $prove) {
    yield $prove
        ->test(
            'Test reduce Sequence to the minimum values',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayOutputVia($io)
                    ->displayErrorVia($io)
                    ->usePrinter(Standard::withoutColors())
                    ->tryToProve(static function($prove) {
                        yield $prove
                            ->proof('must not contain a "0"')
                            ->given(
                                Set::sequence(Set::integers()->above(0))->atLeast(1),
                            )
                            ->test(static function($assert, $values) {
                                foreach ($values as $value) {
                                    $assert->same(0, $value);
                                }
                            });
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
        )
        ->tag(Tag::ci, Tag::local);
};
