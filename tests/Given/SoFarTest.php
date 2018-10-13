<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Given;

use Innmind\BlackBox\{
    Given\SoFar,
    Given\Scenario,
    Given\InitialValue\Name,
};
use PHPUnit\Framework\TestCase;

class SoFarTest extends TestCase
{
    public function testAdd()
    {
        $soFar = new SoFar;

        $soFar2 = $soFar->add(new Name('foo'), 'watev');

        $this->assertInstanceOf(SoFar::class, $soFar2);
        $this->assertNotSame($soFar, $soFar2);
        $this->assertSame('watev', $soFar2->foo);
        $this->assertSame('watev', $soFar2->get('foo'));

        $this->expectException(\LogicException::class);

        $soFar->foo;
    }

    public function testScenario()
    {
        $scenario = (new SoFar)->add(new Name('foo'), 42)->scenario();

        $this->assertInstanceOf(Scenario::class, $scenario);
        $this->assertSame(42, $scenario->foo);
    }
}
