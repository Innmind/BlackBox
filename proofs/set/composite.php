<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Application,
    Runner\IO\Collect,
    Set,
    Tag,
};

return static function($prove) {
    yield $prove
        ->test(
            'Test reduce Composite to the minimum values',
            static function($assert) {
                $io = Collect::new();

                $result = Application::new([])
                    ->displayVia($io)
                    ->mapPrinter(static fn($printer) => $printer->withoutColors())
                    ->tryToProve(static function($prove) {
                        yield $prove
                            ->proof('must not contain an "a"')
                            ->given(Set::compose(
                                static fn($a, $b, $c) => [$a, $b, $c],
                                Set::integers(),
                                Set::strings()->atLeast(1)->filter(
                                    static fn($value) => \str_contains($value, 'a'),
                                ),
                                Set::integers(),
                            ))
                            ->test(static function($assert, $composite) {
                                $assert->string($composite[1])->not()->contains('a');
                            });
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
        )
        ->tag(Tag::ci, Tag::local);
};
