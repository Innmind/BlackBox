<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure\Comparison,
};

final class Resource
{
    private Stats $stats;
    /** @var resource */
    private $resource;

    /**
     * @param resource $resource
     */
    private function __construct(Stats $stats, $resource)
    {
        $this->stats = $stats;
        $this->resource = $resource;
    }

    /**
     * @param resource $resource
     */
    public static function of(Stats $stats, $resource): self
    {
        return new self($stats, $resource);
    }
}
