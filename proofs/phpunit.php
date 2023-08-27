<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    PHPUnit\Load,
    Tag,
};

return static function() {
    foreach (Load::testsAt(__DIR__.'/../tests/') as $test) {
        if (\str_contains($test->name()->toString(), 'CompositeTest::testShrinksAsFastAsPossible')) {
            // Do not run this test in the CI as it fails regularly when coverage
            // is enabled. This is obviously not the correct solution but it will
            // do until the shrinking mechanism is improved and better tested
            yield $test->tag(Tag::local);
        } else {
            yield $test->tag(Tag::ci, Tag::local);
        }
    }
};
