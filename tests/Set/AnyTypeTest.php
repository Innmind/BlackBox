<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\AnyType,
    Set,
    Random\MtRand,
};

class AnyTypeTest extends TestCase
{
    public function testAny()
    {
        $uuids = AnyType::any();

        $this->assertInstanceOf(Set::class, $uuids);
        $this->assertCount(100, \iterator_to_array($uuids->values(new MtRand)));
    }
}
