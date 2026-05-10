<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\BlackBox\PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function unwrap(Set $values): array
    {
        return \iterator_to_array($values->enumerate());
    }
}
