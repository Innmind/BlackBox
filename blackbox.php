<?php
declare(strict_types = 1);

require 'vendor/autoload.php';

use Innmind\BlackBox\{
    Application,
    Runner\Load,
    Runner\CodeCoverage,
    Runner\IO\Collect,
};
use function Innmind\BlackBox\Runner\test;

// This test has to be done here because other tests use global functions
$result = Application::new([])
    ->disableGlobalFunctions()
    ->tryToProve(function() {
        yield test(
            'Global functions can be disabled',
            static fn($assert) => $assert->true(
                Application::new([])
                    ->disableGlobalFunctions()
                    ->displayOutputVia(Collect::new())
                    ->displayErrorVia(Collect::new())
                    ->tryToProve(function() {
                        yield test(
                            'Test function is not declared',
                            static fn($assert) => $assert->false(
                                \function_exists('test'),
                            ),
                        );
                    })
                    ->successful(),
            ),
        );
    });

if (!$result->successful()) {
    $result->exit();
}

Application::new($argv)
    ->codeCoverage(
        CodeCoverage::of(
            __DIR__.'/src/',
            __DIR__.'/proofs/',
            __DIR__.'/fixtures/',
            __DIR__.'/tests/',
        )
            ->dumpTo('coverage.clover')
            ->enableWhen(\getenv('ENABLE_COVERAGE') !== false),
    )
    ->map(static fn($app) => match (\getenv('BLACKBOX_ENV')) {
        'coverage' => $app->scenariiPerProof(1),
        'extensive' => $app->scenariiPerProof(1000),
        default => $app,
    })
    ->tryToProve(Load::everythingIn(__DIR__.'/proofs/'))
    ->exit();
