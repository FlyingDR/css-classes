<?php

declare(strict_types=1);

namespace Flying\Util\Css\Tests\Twig;

use Flying\Util\Css\Twig\ClassesExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class ClassesExtensionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $this->assertEquals('', $this->render('{{ classes() }}'));
        $this->assertEquals('a b c', $this->render('{{ classes("a", "b c") }}'));
        $this->assertEquals('a b c', $this->render('{{ classes(cls) }}', ['cls' => ['a', ['b', ['c']]]]));
        $this->assertEquals('a b', $this->render('{{ classes("a", null, cond ? "b" : "c") }}', ['cond' => true]));
    }

    public function testListConstructionInsideTemplate(): void
    {
        $this->assertEquals('a b c', $this->render('{{ classes("a").with("b").with("c") }}'));
        $this->assertEquals('x z a', $this->render('{{ classes("x y z").with("a").without("y") }}'));
        $this->assertEquals('yes', $this->render('{{ classes("x y z").has("y") ? "yes" : "no" }}'));
    }

    private function render(string $template, array $context = []): string
    {
        $twig = new Environment(new ArrayLoader(['test' => $template]));
        $twig->addExtension(new ClassesExtension());
        /** @noinspection PhpUnhandledExceptionInspection */
        return $twig->render('test', $context);
    }
}
