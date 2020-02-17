<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Uuid,
    Set,
    Set\Value,
};
use Ramsey\Uuid\Uuid as U;

class UuidTest extends TestCase
{
    public function testAny()
    {
        $uuids = Uuid::any();

        $this->assertInstanceOf(Set::class, $uuids);
        $this->assertCount(100, \iterator_to_array($uuids->values()));

        foreach ($uuids->values() as $uuid) {
            $this->assertInstanceOf(Value::class, $uuid);
            $this->assertTrue($uuid->isImmutable());
            $this->assertRegExp(
                '~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~',
                $uuid->unwrap(),
            );
            $this->assertTrue(U::isValid($uuid->unwrap()));
        }
    }

    public function testUuidsAreNotShrinkable()
    {
        $uuids = Uuid::any();

        foreach ($uuids->values() as $uuid) {
            $this->assertFalse($uuid->shrinkable());
        }
    }
}
