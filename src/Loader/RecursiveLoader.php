<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Loader;

use Innmind\BlackBox\Loader;
use Innmind\Url\{
    PathInterface,
    Path,
};

final class RecursiveLoader implements Loader
{
    private $load;

    public function __construct(Loader $load)
    {
        $this->load = $load;
    }

    public function __invoke(PathInterface $path): \Generator
    {
        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator((string) $path)
            ),
            '~^.+\.php$~'
        );

        foreach ($files as $file => $info) {
            if ($info->isDir()) {
                continue;
            }

            yield from ($this->load)(new Path($file));
        }
    }
}
