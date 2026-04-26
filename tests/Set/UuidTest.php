<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Value,
    Random,
};
use Ramsey\Uuid\Uuid as U;

class UuidTest extends TestCase
{
    public function testAny()
    {
        $uuids = Set::uuid()->take(100);

        $this->assertInstanceOf(Set::class, $uuids);
        $this->assertCount(100, \iterator_to_array($uuids->values(Random::mersenneTwister)));

        foreach ($uuids->values(Random::mersenneTwister) as $uuid) {
            $this->assertInstanceOf(Value::class, $uuid);
            $this->assertMatchesRegularExpression(
                '~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~',
                $uuid->unwrap(),
            );
            $this->assertTrue(U::isValid($uuid->unwrap()));
        }
    }

    public function testUuidsAreNotShrinkable()
    {
        $uuids = Set::uuid()->take(100);

        $min = static function($value, $type) use (&$min) {
            $shrunk = $value->shrink();

            return $shrunk ? $min($shrunk->{$type}(), $type) : $value->unwrap();
        };

        foreach ($uuids->values(Random::mersenneTwister) as $uuid) {
            $this->assertSame($uuid->unwrap(), $min($uuid, 'a'));
            $this->assertSame($uuid->unwrap(), $min($uuid, 'b'));
        }
    }
}
