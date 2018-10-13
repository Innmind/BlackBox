<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Given;

use Innmind\Immutable\StreamInterface;

interface InitialValue
{
    public function dependOn(self $initialValue): self;

    /**
     * @return StreamInterface<SoFar>
     */
    public function sets(): StreamInterface;
}
