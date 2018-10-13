<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Given\InitialValue;

use Innmind\BlackBox\{
    Given\InitialValue\Name,
    Exception\DomainException,
};
use function Innmind\BlackBox\Set\strings;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testInterface()
    {
        foreach (strings() as $string) {
            $this->assertSame($string, (string) new Name($string));
        }
    }

    public function testThrowWhenEmptyString()
    {
        $this->expectException(DomainException::class);

        new Name('');
    }
}
