# Changelog

## 6.0.0 - 2025-03-12

### Added

- `Innmind\BlackBox\Set\Provider` to allow classes to be factories of `Set`s without having to implement the `Set` interface
- Support for PHPUnit `12`
- `Innmind\BlackBox\Set::flatMap()` and `Innmind\BlackBox\Set\Seed`
- Callables for `Set::map()`, `Set\Composite`, `Set\Decorate`, `Set\FromGenerator` and `Set\Call` can now also return `Seed`ed values

### Changed

- BlackBox now also accepts instances of `Set\Provider` as input where it accepts `Set`s
- `Innmind\BlackBox\Set` is now a `final class`
- All classes that implemented the `Set` interface are now declared `@internal`
- The template param of `Innmind\BlackBox\Set` is now covariant for more flexibility
- `Innmind\BlackBox\Set\Value` is now declared `@internal`

### Deprecated

- `Innmind\BlackBox\Set\Call::of()`, use `Innmind\BlackBox\Set::call()` instead
- `Innmind\BlackBox\Set\Chars::any()`, use `Innmind\BlackBox\Set::strings()->chars()` instead
- `Innmind\BlackBox\Set\Chars::lowercaseLetter()`, use `Innmind\BlackBox\Set::strings()->chars()->lowercaseLetter()` instead
- `Innmind\BlackBox\Set\Chars::uppercaseLetter()`, use `Innmind\BlackBox\Set::strings()->chars()->uppercaseLetter()` instead
- `Innmind\BlackBox\Set\Chars::number()`, use `Innmind\BlackBox\Set::strings()->chars()->number()` instead
- `Innmind\BlackBox\Set\Chars::ascii()`, use `Innmind\BlackBox\Set::strings()->chars()->ascii()` instead
- `Innmind\BlackBox\Set\Chars::alphanumerical()`, use `Innmind\BlackBox\Set::strings()->chars()->alphanumerical()` instead
- `Innmind\BlackBox\Set\Composite::immutable()`, use `Innmind\BlackBox\Set::compose()->immutable()` instead
- `Innmind\BlackBox\Set\Composite::mutable()`, use `Innmind\BlackBox\Set::compose()->mutable()` instead
- `Innmind\BlackBox\Set\Decorate::immutable()`, use `Innmind\BlackBox\Set->map()` instead
- `Innmind\BlackBox\Set\Decorate::mutable()`, use `Innmind\BlackBox\Set::decorate()` instead
- `Innmind\BlackBox\Set\Either::any()`, use `Innmind\BlackBox\Set::either()` instead
- `Innmind\BlackBox\Set\Elements::of()`, use `Innmind\BlackBox\Set::of()` instead
- `Innmind\BlackBox\Set\Email::any()`, use `Innmind\BlackBox\Set::email()` instead
- `Innmind\BlackBox\Set\FromGenerator::of()`, use `Innmind\BlackBox\Set::generator()` instead
- `Innmind\BlackBox\Set\FromGenerator::mutable()`, use `Innmind\BlackBox\Set::generator()->mutable()` instead
- `Innmind\BlackBox\Set\Integers::any()`, use `Innmind\BlackBox\Set::integers()` instead
- `Innmind\BlackBox\Set\Integers::between()`, use `Innmind\BlackBox\Set::integers()->between()` instead
- `Innmind\BlackBox\Set\Integers::above()`, use `Innmind\BlackBox\Set::integers()->above()` instead
- `Innmind\BlackBox\Set\Integers::below()`, use `Innmind\BlackBox\Set::integers()->below()` instead
- `Innmind\BlackBox\Set\IntegersExceptZero::any()`, use `Innmind\BlackBox\Set::integers()->exceptZero()` instead
- `Innmind\BlackBox\Set\NaturalNumbers::any()`, use `Innmind\BlackBox\Set::integers()->naturalNumbers()` instead
- `Innmind\BlackBox\Set\NaturalNumbersExceptZero::any()`, use `Innmind\BlackBox\Set::integers()->naturalNumbersExceptZero()` instead
- `Innmind\BlackBox\Set\Nullable::of()`, use `Innmind\BlackBox\Set->nullable()` instead
- `Innmind\BlackBox\Set\Randomize::of()`, use `Innmind\BlackBox\Set->randomize()` instead
- `Innmind\BlackBox\Set\RealNumbers::any()`, use `Innmind\BlackBox\Set::realNumbers()` instead
- `Innmind\BlackBox\Set\RealNumbers::between()`, use `Innmind\BlackBox\Set::realNumbers()->between()` instead
- `Innmind\BlackBox\Set\RealNumbers::above()`, use `Innmind\BlackBox\Set::realNumbers()->above()` instead
- `Innmind\BlackBox\Set\RealNumbers::below()`, use `Innmind\BlackBox\Set::realNumbers()->below()` instead
- `Innmind\BlackBox\Set\Sequence::of()`, use `Innmind\BlackBox\Set::sequence()` instead
- `Innmind\BlackBox\Set\Strings::any()`, use `Innmind\BlackBox\Set::strings()` instead
- `Innmind\BlackBox\Set\Strings::between()`, use `Innmind\BlackBox\Set::strings()->between()` instead
- `Innmind\BlackBox\Set\Strings::atMost()`, use `Innmind\BlackBox\Set::strings()->atMost()` instead
- `Innmind\BlackBox\Set\Strings::atLeast()`, use `Innmind\BlackBox\Set::strings()->atLeast()` instead
- `Innmind\BlackBox\Set\Strings::madeOf()`, use `Innmind\BlackBox\Set::strings()->madeOf()` instead
- `Innmind\BlackBox\Set\Tuple::of()`, use `Innmind\BlackBox\Set::tuple()` instead
- `Innmind\BlackBox\Set\Type::any()`, use `Innmind\BlackBox\Set::type()` instead
- `Innmind\BlackBox\Set\Unicode::strings()`, use `Innmind\BlackBox\Set::strings()->unicode()` instead
- `Innmind\BlackBox\Set\Unicode::any()`, use `Innmind\BlackBox\Set::strings()->unicode()->char()` instead
- `Innmind\BlackBox\Set\UnsafeStrings::any()`, use `Innmind\BlackBox\Set::strings()->unsafe()` instead
- `Innmind\BlackBox\Set\Uuid::any()`, use `Innmind\BlackBox\Set::uuid()` instead

## 5.10.1 - 2025-01-19

### Fixed

- Support for PHP `8.4`

## 5.10.0 - 2024-11-30

### Added

- Support for PHPUnit 11

## 5.9.0 - 2024-11-29

### Added

- By default the printer groups the proofs output in a GitHub Action
- `Innmind\BlackBox\Runner\Printer\Standard::disableGitHubOutput()`

## 5.8.0 - 2024-11-09

### Added

- `$assert->array()->contains()`
- `$assert->array()->not()->contains()`

## 5.7.0 - 2024-07-21

### Added

- `Innmind\BlackBox\PHPUnit\Framework\TestCase::assert()`
- `Innmind\BlackBox\Set\Tuple`

### Fixed

- `$assert->same()` message not being used
- `Innmind\BlackBox\Set\Sequence` shrinking process now correctly shrinks all values that do not affect the test
- `Innmind\BlackBox\Set\Composite` shrinking process now correctly shrinks all values that do not affect the test

## 5.6.3 - 2024-02-19

### Fixed

- The global function `property` return type is now `Innmind\BlackBox\Runner\Proof\Property` allowing you to name the property

## 5.6.2 - 2024-01-28

### Fixed

- Thrown exceptions in properties weren't failing proofs

## 5.6.1 - 2023-12-02

### Changed

- When disabling memory limit it now longer tries to reset the memory limit to the previous value. (It seems PHP prevents setting the limit lower to the peak memory usage)
- `Innmind\BlackBox\Set\Call` regenarate the value each time a scenario is run

### Fixed

- Clear the memory before printing a failing scenarion to make sure no values bleed between scenarii.

## 5.6.0 - 2023-12-02

### Added

- Support for `symfony/var-dumper:~7.0`

### Fixed

- Stats are correctly displayed in GitHub Actions even in case of a failure

## 5.5.4 - 2023-11-01

### Fixed

- Assertion failures in properties were displayed as unexpected exceptions

## 5.5.3 - 2023-10-22

### Fixed

- Uncaught exceptions in properties no longer crash the process

### Deprecated

- `Innmind\BlackBox\PHPUnit\Framework\TestCase::expectException()`
- `Innmind\BlackBox\PHPUnit\Framework\TestCase::expectExceptionCode()`
- `Innmind\BlackBox\PHPUnit\Framework\TestCase::expectExceptionMessage()`

## 5.5.2 - 2023-09-18

### Fixed

- `Innmind\BlackBox\Set\MutuallyExclusive` wasn't excluding strings in a different case

## 5.5.1 - 2023-09-13

### Fixed

- Custom tags loaded from PHPUnit groups were discarded

## 5.5.0 - 2023-09-13

### Added

- `Innmind\BlackBox\PHPUnit\Load::parseTagWith()`
- `Innmind\BlackBox\Application::stopOnFailure()`
- `Innmind\BlackBox\Application::map()`
- `Innmind\BlackBox\Application::when()`

## 5.4.0 - 2023-09-02

### Added

- For iTerm users a mark is now placed on failures allowing you to directly jump between errors

## 5.3.0 - 2023-08-27

### Added

- `Innmind\BlackBox\PHPUnit\Framework\TestCase`
- `Innmind\BlackBox\PHPUnit\Load`
- `Innmind\BlackBox\Application::disableMemoryLimit()`
- `Innmind\BlackBox\Runner\Assert::matches()`
- `Innmind\BlackBox\Runner\Assert::time()`
- `Innmind\BlackBox\Runner\Assert::memory()`
- `Innmind\BlackBox\Set\Slice`
- `Innmind\BlackBox\Set\MutuallyExclusive`
- `Innmind\BlackBox\Tag::ci`
- `Innmind\BlackBox\Tag::local`

### Fixed

- The message `Failing scenarii:` was always printed in PHPUnit output even when there was no failures

## 5.2.0 - 2023-08-19

### Added

- `Innmind\BlackBox\PHPUnit\Compatibility::asDataProvider()`
- `Innmind\BlackBox\Application::disableGlobalFunctions()`
- `Innmind\BlackBox\Runner\proof` function
- `Innmind\BlackBox\Runner\test` function
- `Innmind\BlackBox\Runner\given` function
- `Innmind\BlackBox\Runner\property` function
- `Innmind\BlackBox\Runner\properties` function
- PHPUnit extension now prints the generated data that resulted in an error

### Changed

- `Innmind\BlackBox\PHPUnit\BlackBox::forAll()` is now a `static` method

## 5.1.2 - 2023-07-30

### Fixed

- Increased the randomness between scenarii to avoid collisions when validating a model with state.

## 5.1.1 - 2023-07-29

### Changed

- `Innmind\BlackBox\Set\Call` now regenerate the value when shrinking

## 5.1.0 - 2023-07-14

### Added

- `Innmind\BlackBox\Set\Call`

## 5.0.0 - 2023-05-29

### Added

- `Innmind\BlackBox\Set\Nullable`
- `Innmind\BlackBox\Set::map()`
- `Innmind\BlackBox\PHPUnit\Extension`
- `Innmind\BlackBox\Application`
- `Innmind\BlackBox\Tag`
- `Innmind\BlackBox\Runner\Load`
- `Innmind\BlackBox\Runner\CodeCoverage`
- `Innmind\BlackBox\Runner\Assert`
- `Innmind\BlackBox\Runner\Printer`
- `Innmind\BlackBox\Runner\IO`
- `Innmind\BlackBox\Property::any()`

### Changed

- All `Set`s constructor are now private
- PHP `8.2` is now required
- `Innmind\BlackBox\Set\AnyType` has been renamed to `Innmind\BlackBox\Set\Type`
- `Innmind\BlackBox\Random` is now an enum
- Requires `PHPUnit` `10`
- `Innmind\BlackBox\Properties` constructor is now private, use `::of()` named constructor instead
- `Innmind\BlackBox\Properties::ensureHeldBy` now expects `Innmind\BlackBox\Runner\Assert` as a first argument
- `Innmind\BlackBox\Properties` is now longer compatible with third party test runners
- `Innmind\BlackBox\Property::ensureHeldBy` now expects `Innmind\BlackBox\Runner\Assert` as a first argument
- `Innmind\BlackBox\Property` is now longer compatible with third party test runners
- `Innmind\BlackBox\Set::take()` and `::filter()` now explicitly state that they are mutation free
- `Innmind\BlackBox\Set::take()` now requires a `positive-int` as argument
- `Innmind\BlackBox\Set\Chars` now longer implements the `Set` interface, use `Chars::any()` instead
- `Innmind\BlackBox\Set\IntegersExceptZero` now longer implements the `Set` interface, use `IntegersExceptZero::any()` instead
- `Innmind\BlackBox\Set\NaturalNumbers` now longer implements the `Set` interface, use `NaturalNumbers::any()` instead
- `Innmind\BlackBox\Set\NaturalNumbersExceptZero` now longer implements the `Set` interface, use `NaturalNumbersExceptZero::any()` instead
- `Innmind\BlackBox\Set\Sequence::of()` now longer accept the range of values as 2nd argument, use `Sequence::of()->atLeast()`, `Sequence::of()->atMost()` and `Sequence::of()->between()` instead
- `Innmind\BlackBox\Set\Strings` now longer implements the `Set` interface, use `Strings::any()` instead
- `Innmind\BlackBox\Set\Strings::any()` now longer accept the maximum length, use `Strings::atMost()` instead

### Fixed

- Avoid trying to generate from a `Set` in `Set\Either` that can't generate data

### Removed

- `seeder` method provided by `Innmind\BlackBox\PHPUnit\Blackbox` trait
- Support for PHPUnit `8` and `9` (custom printers are now longer available)
- Support for Symfony `4` and `5`
- `Innmind\BlackBox\Set\Regex`
- `Innmind\BlackBox\Set\Strings::matching()`
- `Innmind\BlackBox\Random\RandomInt`
- `Innmind\BlackBox\Random\MtRand`
- `Innmind\BlackBox\Property::name()`
- `Innmind\BlackBox\PHPUnit\BlackBox::seeder()`
- `Innmind\BlackBox\PHPUnit\Seeder`
- `Innmind\BlackBox\Set\Properties::chooseFrom()`, use `Properties::any()->between()` or `Properties::any()->atMost()` instead
- `Innmind\BlackBox\Set\Property`
- `Innmind\BlackBox\Set\Unicode::lengthBetween()`, use `Unicode::any()->between()` instead
