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
}
