<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\IO;

use Innmind\BlackBox\Runner\IO;

enum Standard implements IO
{
    case output;

    #[\Override]
    public function __invoke(string $data): void
    {
        // use echo instead of \fwrite(\STDOUT) because it is less prone
        // to crashing and being unable to display the data
        echo $data;
    }
}
