<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Value,
    PHPUnit\BlackBox,
    Random,
};

class EmailTest extends TestCase
{
    use BlackBox;

    public function testAny()
    {
        $emails = Set::email();

        $this->assertInstanceOf(Set::class, $emails);
        $this->assertCount(100, \iterator_to_array($emails->values(Random::mersenneTwister)));

        foreach ($emails->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->immutable());
            $this->assertIsString($value->unwrap());
        }
    }

    public function testEmailsAreValid()
    {
        $this
            ->forAll(Set::email())
            ->then(function($email) {
                $this->assertNotFalse(\filter_var($email, \FILTER_VALIDATE_EMAIL));
            });
    }

    public function testEmailsAreShrinkable()
    {
        $emails = Set::email();

        foreach ($emails->values(Random::mersenneTwister) as $email) {
            $this->assertNotNull($email->shrink());
        }
    }
}
