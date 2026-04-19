<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    PHPUnit\Framework\TestCase,
    PHPUnit\Framework\Attributes\DataProvider,
    Set,
};
use PHPUnit\Framework\Attributes\DataProvider as PHPUnitDataProvider;

class BlackBoxTest extends TestCase
{
    use BlackBox;

    #[DataProvider('ints')]
    public function testDataProviderCompatibility($a, $b)
    {
        $this->assertIsInt($a);
        $this
            ->assert()
            ->number($b)
            ->int();
    }

    #[DataProvider('ints2')]
    #[PHPUnitDataProvider('ints2')]
    public function testMultipleDataProviders($a, $b)
    {
        $this->assertIsInt($a);
        $this
            ->assert()
            ->number($b)
            ->int();
    }

    public static function ints(): iterable
    {
        return self::forAll(
            Set::integers(),
            Set::integers(),
        )->asDataProvider();
    }

    public static function ints2(): iterable
    {
        return self::forAll(
            Set::integers(),
            Set::integers(),
        )
            ->take(10)
            ->asDataProvider();
    }
}
