<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\Url\PathInterface;
use Innmind\Immutable\StreamInterface;

interface Loader
{
    /**
     * @return StreamInterface<Generator>
     */
    public function __invoke(PathInterface $path): StreamInterface;
}
