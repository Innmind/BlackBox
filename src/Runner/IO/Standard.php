<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\IO;

use Innmind\BlackBox\Runner\IO;

enum Standard implements IO
{
    case output;
    case error;

    public function __invoke(string $data): void
    {
        match ($this) {
            self::output => (static function(string $data) {
                // use echo instead of \fwrite(\STDOUT) because it is less prone
                // to crashing and being unable to display the data
                echo $data;
            })($data),
            // TODO crash when stream no longer writable ?
            self::error => \fwrite(\STDERR, $data),
        };
    }
}
