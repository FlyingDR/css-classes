<?php

declare(strict_types=1);

namespace Flying\Util\Css\Tests;

use Flying\Util\Css\Classes;
use Flying\Util\Css\ClassesInterface;
use Flying\Util\Css\MutableClasses;
use PHPUnit\Framework\TestCase;

abstract class AbstractClassesTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $classes = $this->createClasses('a', 'b', 'c');
        $this->assertEquals('a b c', $classes);
    }

    public function dpPassingDifferentValues(): iterable
    {
        // Test passing no values at all
        yield [
            [],
            '',
        ];
        // Test passing single value
        yield [
            ['a'],
            'a',
        ];
        // Test passing multiple values
        yield [
            ['a', 'b', 'c'],
            'a b c',
        ];
        // Test passing same value multiple times
        yield [
            ['a', 'a', 'a'],
            'a',
        ];
        // Test passing case-sensitive
        yield [
            ['a', 'A', 'ab', 'aB'],
            'a A ab aB',
        ];
        // Test passing invalid values
        yield [
            [null, true, false, 42, 1.2345, [], new \stdClass(), fn() => 'some-class'],
            '',
        ];
        // Test passing nested values
        yield [
            ['a', ['b', ['c', ['d', ['e', ['f']]]]]],
            'a b c d e f',
        ];
        // Test passing iterable
        yield [
            [new \ArrayIterator(['a', 'b', 'c'])],
            'a b c',
        ];
        // Test passing own instance
        yield [
            [$this->createClasses('a b c d e f')],
            'a b c d e f',
        ];
        // Test passing instance of available implementations
        yield [
            [new Classes('a b c d e f'), new MutableClasses('x y z')],
            'a b c d e f x y z',
        ];
    }

    /**
     * @dataProvider dpRemovingClasses
     */
    public function testRemovingClasses(string $initial, array $remove, string $expected): void
    {
        $this->assertEquals($expected, $this->createClasses($initial)->without(...$remove));
    }

    public function dpRemovingClasses(): iterable
    {
        // Empty removal list
        yield [
            'a b c d e f',
            [],
            'a b c d e f',
        ];
        // Single value
        yield [
            'a b c d e f',
            ['a'],
            'b c d e f',
        ];
        // Multiple values
        yield [
            'a b c d e f',
            ['a', 'c', 'e'],
            'b d f',
        ];
        // Repetitive values
        yield [
            'a b c d e f',
            ['a', 'a', 'a'],
            'b c d e f',
        ];
        // Case-sensitive values
        yield [
            'a b c d e f',
            ['A', 'B', 'C'],
            'a b c d e f',
        ];
        // Non-existing values
        yield [
            'a b c d e f',
            ['x', 'y', 'z'],
            'a b c d e f',
        ];
        // Invalid values
        yield [
            'a b c d e f',
            [null, true, false, 42, 1.2345, [], new \stdClass()],
            'a b c d e f',
        ];
        // Nesting values
        yield [
            'a b c d e f',
            ['a', ['b', ['c', ['d', ['e']]]]],
            'f',
        ];
    }

    public function testFilteringClassesUsingCustomFilter(): void
    {
        /** @noinspection OneTimeUseVariablesInspection */
        $classes = $this->createClasses('a bb ccc dddd eeeee ffffff');
        $filtered = $classes->filter(fn(string $class): bool => strlen($class) % 2 === 0);
        $this->assertEquals('bb dddd ffffff', $filtered);
    }

    public function testItIsPossibleToCheckExistenceOfTheClassInList(): void
    {
        $classes = $this->createClasses('a b c');
        $this->assertTrue($classes->has('a'));
        $this->assertFalse($classes->has('A'));
        $this->assertFalse($classes->has('x'));
    }

    public function testClearingClassesList(): void
    {
        $classes = $this->createClasses('a b c');
        $this->assertCount(3, $classes);
        $this->assertCount(0, $classes->clear());
    }

    public function testCountingEntries(): void
    {
        $this->assertInstanceOf(\Countable::class, new Classes());
        $this->assertCount(0, new Classes());
        $this->assertCount(1, new Classes('abc'));
        $this->assertCount(3, new Classes('a b c'));
    }

    public function testClassesListIsIterable(): void
    {
        $this->assertInstanceOf(\IteratorAggregate::class, new Classes());
        $classes = $this->createClasses('a b c');
        $exported = [];
        /** @noinspection MissUsingForeachInspection */
        foreach ($classes as $class) {
            $exported[] = $class;
        }
        $this->assertEquals(['a', 'b', 'c'], $exported);
    }

    public function testClassesListIsExportableToArray(): void
    {
        $classes = $this->createClasses('a b c');
        $this->assertEquals(['a', 'b', 'c'], $classes->toArray());
    }

    public function testClassesListIsConvertableToString(): void
    {
        $this->assertInstanceOf(\Stringable::class, new Classes());
        $classes = $this->createClasses('a')->with('b')->with('c');
        $this->assertEquals('a b c', (string)$classes);
    }

    abstract protected function createClasses(mixed ...$args): ClassesInterface;
}
