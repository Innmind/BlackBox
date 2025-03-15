<?php
declare(strict_types = 1);

use Innmind\BlackBox\{
    Set,
    Random,
    Tag,
};

return static function() {
    yield test(
        'Set::strings()->unsafe() shrink to an empty string',
        static function($assert) {
            foreach (Set::strings()->unsafe()->values(Random::default) as $value) {
                while ($shrunk = $value->shrink()) {
                    $value = $shrunk->a();
                }

                $assert->same('', $value->unwrap());
            }
        },
    )->tag(Tag::ci, Tag::local);
};
