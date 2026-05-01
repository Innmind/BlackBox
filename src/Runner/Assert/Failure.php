<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\Assert\Failure\{
    Truth,
    Property,
    Comparison,
};

final class Failure extends \Exception
{
    private function __construct(private Truth|Property|Comparison $kind)
    {
    }

    /**
     * @internal
     */
    public static function of(Truth|Property|Comparison $kind): self
    {
        return new self($kind);
    }

    public function kind(): Truth|Property|Comparison
    {
        return $this->kind;
    }
}
