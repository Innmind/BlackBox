<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\MapInterface;

final class Map implements Assertion
{
    private $key;
    private $value;

    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function __invoke(
        ScenarioReport $report,
        Result $result,
        Scenario $scenario
    ): ScenarioReport {
        if (!$result->value() instanceof MapInterface) {
            return $report->fail('Not a map');
        }

        if (
            (string) $result->value()->keyType() !== $this->key ||
            (string) $result->value()->valueType() !== $this->value
        ) {
            return $report->fail(sprintf(
                'Not a map of type <%s, %s>, got <%s, %s>',
                $this->key,
                $this->value,
                $result->value()->keyType(),
                $result->value()->valueType()
            ));
        }

        return $report->success();
    }
}
