<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Email,
    Set,
    Set\Value,
    PHPUnit\BlackBox,
};

class EmailTest extends TestCase
{
    use BlackBox;

    public function testAny()
    {
        $emails = Email::any();

        $this->assertInstanceOf(Set::class, $emails);
        $this->assertCount(100, \iterator_to_array($emails->values()));

        foreach ($emails->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
            $this->assertIsString($value->unwrap());
        }
    }

    public function testEmailsAreValid()
    {
        $this
            ->forAll(Email::any())
            ->then(function($email) {
                $this->assertNotFalse(filter_var($email, FILTER_VALIDATE_EMAIL));
            });
    }

    public function testEmailsAreShrinkable()
    {
        $emails = Email::any();

        foreach ($emails->values() as $email) {
            $this->assertTrue($email->shrinkable());
        }
    }
}
