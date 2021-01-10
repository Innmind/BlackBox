<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Email,
    Set,
    Set\Value,
    PHPUnit\BlackBox,
    Random\MtRand,
};
use ReverseRegex\Lexer;

class EmailTest extends TestCase
{
    use BlackBox;

    public function testAny()
    {
        $this->skipIfUninstalled();

        $emails = Email::any();

        $this->assertInstanceOf(Set::class, $emails);
        $this->assertCount(100, \iterator_to_array($emails->values(new MtRand)));

        foreach ($emails->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
            $this->assertIsString($value->unwrap());
        }
    }

    public function testEmailsAreValid()
    {
        $this->skipIfUninstalled();

        $this
            ->forAll(Email::any())
            ->then(function($email) {
                $this->assertNotFalse(\filter_var($email, \FILTER_VALIDATE_EMAIL));
            });
    }

    public function testEmailsAreShrinkable()
    {
        $this->skipIfUninstalled();

        $emails = Email::any();

        foreach ($emails->values(new MtRand) as $email) {
            $this->assertTrue($email->shrinkable());
        }
    }

    private function skipIfUninstalled()
    {
        if (!\class_exists(Lexer::class)) {
            $this->markTestSkipped();
        }
    }
}
