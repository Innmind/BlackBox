<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Test\Name,
    Test\Report,
};
use Innmind\OperatingSystem\OperatingSystem;

interface Test
{
    public function __invoke(OperatingSystem $os): Report;
}
