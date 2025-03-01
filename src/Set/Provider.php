<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @template T
 */
interface Provider
{
    /**
     * @psalm-mutation-free
     *
     * @return Set<T>
     */
    public function toSet(): Set;
}
