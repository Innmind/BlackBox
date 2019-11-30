<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Uuid,
    Set,
};
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    public function testAny()
    {
        $uuids = Uuid::any();

        $this->assertInstanceOf(Set::class, $uuids);
        $this->assertCount(100, \iterator_to_array($uuids->values()));

        foreach ($uuids->values() as $uuid) {
            $this->assertRegExp(
                '~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~',
                $uuid
            );
        }
    }
}
