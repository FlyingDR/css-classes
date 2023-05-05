<?php

declare(strict_types=1);

namespace Flying\Util\Css\Twig;

use Flying\Util\Css\Classes;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ClassesExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('classes', \Closure::fromCallable([Classes::class, 'from'])),
        ];
    }
}
