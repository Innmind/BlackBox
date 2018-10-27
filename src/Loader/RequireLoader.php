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

final class RequireLoader implements Loader
{
    public function __invoke(PathInterface $path): \Generator
    {
        if (!\file_exists((string) $path)) {
            throw new FileDoesntExist((string) $path);
        }

        $value = require (string) $path;

        if ($value instanceof Test) {
            yield $value;

            return;
        }

        if (!$value instanceof \Generator) {
            throw new NoTestGeneratorFound((string) $path);
        }

        yield from $value;
    }
}
