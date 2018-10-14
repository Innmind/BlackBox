<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Loader;

use Innmind\BlackBox\Loader;
use Innmind\Url\{
    PathInterface,
    Path,
};
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};

final class RecursiveLoader implements Loader
{
    private $load;

    public function __construct(Loader $load)
    {
        $this->load = $load;
    }

    public function __invoke(PathInterface $path): StreamInterface
    {
        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator((string) $path)
            ),
            '~^.+\.php$~'
        );
        $generators = Stream::of(\Generator::class);

        foreach ($files as $file => $info) {
            if ($info->isDir()) {
                continue;
            }

            $generators = $generators->append(
                ($this->load)(new Path($file))
            );
        }

        return $generators;
    }
}
