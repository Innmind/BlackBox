<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\PHPUnit\ResultPrinterV8;
use PHPUnit\Framework\{
    TestCase,
    TestResult,
    Test,
};
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class ResultPrinterV8Test extends TestCase
{
    use BlackBox;

    /** @group failing-on-purpose */
    public function testPrintDataSet()
    {
        $this
            ->forAll(
                Set\Integers::any(),
                Set\Decorate::immutable(
                    function($i) {
                        $std = new \stdClass;
                        $std->prop = $i;

                        return $std;
                    },
                    Set\Integers::any(),
                ),
            )
            ->then(function() {
                $this->assertTrue(false);
            });
    }

    /** @group failing-on-purpose */
    public function testPrintDataSetWhenUnexpectedExceptionIsThrown()
    {
        $this
            ->forAll(Set\Strings::any())
            ->then(function($string) {
                throw new \LogicException($string);
            });
    }

    /** @group failing-on-purpose */
    public function testPrintDataSetWhenExceptionIsDifferentThanTheExpectedOne()
    {
        $this
            ->forAll(Set\Strings::any())
            ->then(function($string) {
                $this->expectException(\RuntimeException::class);
                $this->expectExceptionMessage($string);

                throw new \LogicException($string);
            });
    }

    /** @group failing-on-purpose */
    public function testPrintDataSetWhenExceptionMessageIsDifferentThanTheExpectedOne()
    {
        $this
            ->forAll(Set\Strings::any())
            ->then(function($string) {
                $this->expectException(\LogicException::class);
                $this->expectExceptionMessage('foo');

                throw new \LogicException($string);
            });
    }

    /** @group failing-on-purpose */
    public function testPrintDataSetWhenExceptionCodeIsDifferentThanTheExpectedOne()
    {
        $this
            ->forAll(Set\Strings::any(), Set\Integers::above(0))
            ->then(function($string, $code) {
                $this->expectException(\LogicException::class);
                $this->expectExceptionMessage($string);
                $this->expectExceptionCode(42);

                throw new \LogicException($string, $code);
            });
    }
}
