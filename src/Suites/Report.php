<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Suites;

use Innmind\BlackBox\Test;
use Innmind\Immutable\StreamInterface;

interface Report
{
    public function add(Test\Report $report): self;

    /**
     * @return StreamInterface<Test\Report>
     */
    public function failures(): StreamInterface;
}
