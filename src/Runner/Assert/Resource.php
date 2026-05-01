<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\Stats;

final class Resource
{
    /**
     * @param resource $resource
     */
    private function __construct(
        private Stats $stats,
        private $resource,
    ) {
    }

    /**
     * @internal
     *
     * @param resource $resource
     */
    public static function of(Stats $stats, $resource): self
    {
        return new self($stats, $resource);
    }
}
