<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Loader;

use Innmind\BlackBox\{
    Loader,
    Test,
    Exception\FileDoesntExist,
    Exception\NoTestGeneratorFound,
};
use Innmind\Url\PathInterface;
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};

final class RequireLoader implements Loader
{
    public function __invoke(PathInterface $path): StreamInterface
    {
        if (!\file_exists((string) $path)) {
            throw new FileDoesntExist((string) $path);
        }

        $value = require (string) $path;

        if ($value instanceof Test) {
            $value = (function() use ($value) {
                yield $value;
            })();
        }

        if (!$value instanceof \Generator) {
            throw new NoTestGeneratorFound((string) $path);
        }

        return Stream::of(\Generator::class, $value);
    }
}
