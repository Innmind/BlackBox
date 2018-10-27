<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Given;

interface InitialValue
{
    public function dependOn(self $initialValue): self;

    /**
     * @return \Generator<SoFar>
     */
    public function sets(): \Generator;
}
