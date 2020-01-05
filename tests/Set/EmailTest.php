<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Email,
    Set,
    Set\Value,
};

class EmailTest extends TestCase
{
    public function testAny()
    {
        $emails = Email::any();

        $this->assertInstanceOf(Set::class, $emails);
        $this->assertCount(100, \iterator_to_array($emails->values()));

        foreach ($emails->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
            $this->assertIsString($value->unwrap());
            $this->assertNotFalse(filter_var($value->unwrap(), FILTER_VALIDATE_EMAIL));
        }
    }
}
