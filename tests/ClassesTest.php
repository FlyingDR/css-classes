<?php

declare(strict_types=1);

namespace Flying\Util\Css\Tests;

use Flying\Util\Css\Classes;
use PHPUnit\Framework\TestCase;
use function Flying\Util\Css\classes;

class ClassesTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $classes = Classes::from('a', 'b', 'c');
        $this->assertEquals('a b c', $classes);
    }

    /**
     * @dataProvider dpPassingDifferentValues
     */
    public function testPassingDifferentValues(array $args, string $expected): void
    {
        $this->assertEquals($expected, new Classes(...$args));
        $this->assertEquals($expected, Classes::from(...$args));
        $this->assertEquals($expected, (new Classes())->with(...$args));
        $this->assertEquals($expected, classes(...$args));
    }

    public function dpPassingDifferentValues(): array
    {
        return [
            // Test passing no values at all
            [
                [],
                '',
            ],
            // Test passing single value
            [
                ['a'],
                'a',
            ],
            // Test passing multiple values
            [
                ['a', 'b', 'c'],
                'a b c',
            ],
            // Test passing same value multiple times
            [
                ['a', 'a', 'a'],
                'a',
            ],
            // Test passing case-sensitive
            [
                ['a', 'A', 'ab', 'aB'],
                'a A ab aB',
            ],
            // Test passing invalid values
            [
                [null, true, false, 42, 1.2345, [], new \stdClass(), fn() => 'some-class'],
                '',
            ],
            // Test passing nested values
            [
                ['a', ['b', ['c', ['d', ['e', ['f']]]]]],
                'a b c d e f',
            ],
            // Test passing iterable
            [
                [new \ArrayIterator(['a', 'b', 'c'])],
                'a b c',
            ],
            // Test passing own instance
            [
                [Classes::from('a b c d e f')],
                'a b c d e f',
            ],
        ];
    }

    /**
     * @dataProvider dpRemovingClasses
     */
    public function testRemovingClasses(string $initial, array $remove, string $expected): void
    {
        $this->assertEquals($expected, Classes::from($initial)->without(...$remove));
    }

    public function dpRemovingClasses(): array
    {
        return [
            // Empty removal list
            [
                'a b c d e f',
                [],
                'a b c d e f',
            ],
            // Single value
            [
                'a b c d e f',
                ['a'],
                'b c d e f',
            ],
            // Multiple values
            [
                'a b c d e f',
                ['a', 'c', 'e'],
                'b d f',
            ],
            // Repetitive values
            [
                'a b c d e f',
                ['a', 'a', 'a'],
                'b c d e f',
            ],
            // Case-sensitive values
            [
                'a b c d e f',
                ['A', 'B', 'C'],
                'a b c d e f',
            ],
            // Non-existing values
            [
                'a b c d e f',
                ['x', 'y', 'z'],
                'a b c d e f',
            ],
            // Invalid values
            [
                'a b c d e f',
                [null, true, false, 42, 1.2345, [], new \stdClass()],
                'a b c d e f',
            ],
            // Nesting values
            [
                'a b c d e f',
                ['a', ['b', ['c', ['d', ['e']]]]],
                'f',
            ],
        ];
    }

    public function testFilteringClassesUsingCustomFilter(): void
    {
        $classes = Classes::from('a bb ccc dddd eeeee ffffff');
        $filtered = $classes->filter(fn(string $class): bool => strlen($class) % 2 === 0);
        $this->assertEquals('bb dddd ffffff', $filtered);
    }

    public function testItIsPossibleToCheckExistenceOfTheClassInList(): void
    {
        $classes = Classes::from('a b c');
        $this->assertTrue($classes->has('a'));
        $this->assertFalse($classes->has('A'));
        $this->assertFalse($classes->has('x'));
    }

    public function testClearingClassesList(): void
    {
        $classes = Classes::from('a b c');
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
        $classes = Classes::from('a b c');
        $exported = [];
        foreach ($classes as $class) {
            $exported[] = $class;
        }
        $this->assertEquals(['a', 'b', 'c'], $exported);
    }

    public function testClassesListIsExportableToArray(): void
    {
        $classes = Classes::from('a b c');
        $this->assertEquals(['a', 'b', 'c'], $classes->toArray());
    }

    public function testClassesListIsConvertableToString(): void
    {
        $this->assertInstanceOf(\Stringable::class, new Classes());
        $classes = Classes::from('a')->with('b')->with('c');
        $this->assertEquals('a b c', (string)$classes);
    }

    public function testAllOperationsAreImmutable(): void
    {
        $classes = Classes::from('a b c');

        $one = $classes->with('x');
        $this->assertNotSame($classes, $one);
        $this->assertEquals('a b c', $classes);
        $this->assertEquals('a b c x', $one);

        $two = $classes->without('a');
        $this->assertNotSame($classes, $two);
        $this->assertNotSame($one, $two);
        $this->assertEquals('a b c', $classes);
        $this->assertEquals('b c', $two);

        $three = $classes->filter(fn(string $class): bool => $class === 'a');
        $this->assertNotSame($classes, $three);
        $this->assertNotSame($one, $three);
        $this->assertNotSame($two, $three);
        $this->assertEquals('a b c', $classes);
        $this->assertEquals('a', $three);

        $four = $classes->clear();
        $this->assertNotSame($classes, $four);
        $this->assertNotSame($one, $four);
        $this->assertNotSame($two, $four);
        $this->assertNotSame($three, $four);
        $this->assertEquals('a b c', $classes);
        $this->assertEquals('', $four);
    }
}
