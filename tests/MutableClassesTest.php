<?php

declare(strict_types=1);

namespace Flying\Util\Css\Tests;

use Flying\Util\Css\ClassesInterface;
use Flying\Util\Css\MutableClasses;
use function Flying\Util\Css\classes;

class MutableClassesTest extends AbstractClassesTest
{
    /**
     * @dataProvider dpPassingDifferentValues
     */
    public function testPassingDifferentValues(array $args, string $expected): void
    {
        $this->assertEquals($expected, new MutableClasses(...$args));
        $this->assertEquals($expected, MutableClasses::from(...$args));
        $this->assertEquals($expected, (new MutableClasses())->with(...$args));
        $this->assertEquals($expected, classes(...$args));
    }

    public function testAllOperationsAreMutable(): void
    {
        $classes = $this->createClasses('a b c');

        $one = $classes->with('x');
        $this->assertSame($classes, $one);
        $this->assertEquals('a b c x', $classes);

        $two = $classes->without('a');
        $this->assertSame($classes, $two);
        $this->assertSame($one, $two);
        $this->assertEquals('b c x', $classes);

        $three = $classes->filter(fn(string $class): bool => $class === 'b');
        $this->assertSame($classes, $three);
        $this->assertSame($one, $three);
        $this->assertSame($two, $three);
        $this->assertEquals('b', $classes);

        $four = $classes->clear();
        $this->assertSame($classes, $four);
        $this->assertSame($one, $four);
        $this->assertSame($two, $four);
        $this->assertSame($three, $four);
        $this->assertEquals('', $four);

        $classes = $this->createClasses('a bb ccc dddd eeeee ffffff')
            ->with('x', 'yy', 'zzz')
            ->without('a')
            ->filter(fn(string $class): bool => strlen($class) % 2 === 0);
        $this->assertEquals('bb dddd ffffff yy', $classes);
    }

    protected function createClasses(...$args): ClassesInterface
    {
        return new MutableClasses(...$args);
    }
}
