<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Loader;

use Innmind\BlackBox\{
    Loader,
    Exception\NoTestGeneratorFound,
};
use Innmind\Url\PathInterface;

final class SilenceWhenNoGeneratorFound implements Loader
{
    private $load;

    public function __construct(Loader $load)
    {
        $this->load = $load;
    }

    public function __invoke(PathInterface $path): \Generator
    {
        try {
            yield from ($this->load)($path);
        } catch (NoTestGeneratorFound $e) {
            yield from [];
        }
    }
}
