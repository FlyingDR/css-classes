<?php

declare(strict_types=1);

namespace Flying\Util\Css\Tests;

use Flying\Util\Css\Classes;
use Flying\Util\Css\ClassesInterface;
use function Flying\Util\Css\classes;

class ClassesTest extends AbstractClassesTest
{
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

    public function testAllOperationsAreImmutable(): void
    {
        $classes = $this->createClasses('a b c');

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

    protected function createClasses(...$args): ClassesInterface
    {
        return new Classes(...$args);
    }
}
