<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Given;

use Innmind\BlackBox\Given\Scenario;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class ScenarioTest extends TestCase
{
    public function testThrowWhenInvalidKeyType()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 must be of type MapInterface<string, mixed>');

        new Scenario(new Map('foo', 'unchecked'));
    }

    public function testInterface()
    {
        $scenario = new Scenario(
            (new Map('string', 'mixed'))
                ->put('foo', 'bar')
                ->put('bar', 'baz')
        );

        $this->assertSame('bar', $scenario->get('foo'));
        $this->assertSame('baz', $scenario->get('bar'));
        $this->assertSame('bar', $scenario->foo);
        $this->assertSame('baz', $scenario->bar);
    }
}
