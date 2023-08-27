<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    PHPUnit\Load,
    Tag,
};

return static function() {
    foreach (Load::testsAt(__DIR__.'/../tests/') as $test) {
        if ($test->tags() === []) {
            yield $test->tag(Tag::ci, Tag::local);
        } else {
            // this means the tag are already defined via groups
            yield $test;
        }
    }
};
