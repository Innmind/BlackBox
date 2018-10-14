<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Loader;

use Innmind\BlackBox\{
    Loader,
    Exception\NoTestGeneratorFound,
};
use Innmind\Url\PathInterface;
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};

final class SilenceWhenNoGeneratorFound implements Loader
{
    private $load;

    public function __construct(Loader $load)
    {
        $this->load = $load;
    }

    public function __invoke(PathInterface $path): StreamInterface
    {
        try {
            return ($this->load)($path);
        } catch (NoTestGeneratorFound $e) {
            return Stream::of(\Generator::class);
        }
    }
}
