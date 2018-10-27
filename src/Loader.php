<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\Url\PathInterface;

interface Loader
{
    /**
     * @return \Generator<Test>
     */
    public function __invoke(PathInterface $path): \Generator;
}
