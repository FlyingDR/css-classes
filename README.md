# css-classes
Simple library to manage list of CSS classes. It simplifies tasks of constructing list of CSS classes from given information, adding and removing classes from the list.

## Installation

Simply install it using Composer:

```bash
composer require flying/css-classes
```

## Standalone usage

Functionality is exposed via [`\Flying\Util\Css\Classes`](src/Classes.php) class. 

### Accepted values

Any method that is mean to accept CSS classes actually accepts arbitrary amount of arguments of any type (`mixed ...$classes`). Given arguments are processed in this way:

 - Iterables are expanded
 - Nested values are flattened
 - Any values besides non-empty strings are ignored

### Immutability

Class is immutable by its nature, so all mutation methods of the class returns new instance.

### List creation

Classes list can be constructed using either by creating new `Classes` instance or by using `Classes::from()` static method. Both methods are [accepted](#accepted-values) variable amount arguments of any type. 

Example:

```php
$classes = Classes::from('foo', 'bar baz', ['a', 'b', 'c']);
// Outputs: foo bar baz a b c
echo $classes;
$classes = $classes->without('baz');
// Outputs: foo bar a b c
echo $classes;
$classes = $classes->filter(fn(string $class): bool => strlen($class) > 1);
// Outputs: foo bar
echo $classes;
$classes = $classes->with('x', null, true, false, 42, 1.2345, ['y', ['z']]);
// Outputs: foo bar x y z
echo $classes;
// Outputs: 5
echo count($classes);
```

### Classes list modification

These methods can be used to modify list of CSS classes:

 - `with(...$classes)` - Add given CSS classes to the list
 - `without(...$classes)` - Remove given CSS classes from the list
 - `filter(callable $filter)` - Filter CSS classes list using provided filter. `$filter` signature is `(string $class): bool`
 - `clear()` - Remove all CSS classes from the list. Taking [immutability](#immutability) in consideration it is equivalent of `new Classes()`.

### Classes list exporting

Classes list can be exported as a plain array using `toArray()` method. Also class implements `ArrayAggregate` and `Stringable` interfaces, so it is also possible to iterate classes list and cast its value into string.

### Other methods

`Classes` implements `Countable` interface, so amount of available CSS classes can be calculated using `count()` PHP function or by calling `count()` method. 

It is also possible to check if given class is available in list by using `has(string $class)` method.

### Standalone `classes()` function

For simple cases when it is only need to create CSS classes list as a string from available data it is also possible to use `\Flying\Util\Css\classes()` function.

`classes()` function is available since v1.1.0

## Usage in Twig

Library includes `\Flying\Util\Css\Twig\ClassesExtension` Twig extension that exposes `classes` function that provides same functionality as a [main class](#standalone-usage).

It can be used as a simple construction of the classes list:  

```twig
{%- set category = 'some-category' -%}
{%- set active = true -%}
{%- set current = false -%}
{# "main-class some-category is-active" will be generated #}
<div class="{{ classes('main-class', category, active ? 'is-active': null, current ? 'is-current' : null) }}"></div>
```

But it is also possible to use CSS classes list modification methods:

```twig
{%- set categories = {foo: 'Foo', bar: 'Bar'} -%}
{%- set current = 'foo' -%}
<ul class="categories-list">
    {%- set cl = classes('category') -%}
    {% for id, name in categories %}
        {# For "foo" category: "category ctg-foo current" will be generated #}
        {# For "bar" category: "category ctg-bar" will be generated #}
        <li class="{{ cl.with('ctg-' ~ id, current == id ? 'current' : null) }}">{{ name }}</li>
    {% endfor %}
</ul>
```

Refer to the [Twig documentation](https://twig.symfony.com/doc/3.x/api.html#using-extensions) on how to add extension to the Twig. 

## License

Library is licensed under MIT license.
