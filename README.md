# Has Attributes Trait
[![](https://img.shields.io/packagist/php-v/clouding/has-attributes.svg?style=flat-square)](https://packagist.org/packages/clouding/has-attributes)
[![](https://img.shields.io/github/release/cloudingcity/has-attributes.svg?style=flat-square)](https://packagist.org/packages/clouding/has-attributes)
[![](https://img.shields.io/travis/com/cloudingcity/has-attributes.svg?style=flat-square)](https://travis-ci.com/cloudingcity/has-attributes)
[![](https://img.shields.io/codecov/c/github/cloudingcity/has-attributes.svg?style=flat-square)](https://codecov.io/gh/cloudingcity/has-attributes)

A trait to give class attributes

## Features

- Give class attributes with array.
- Define attributes strict type.

## Quick Example

Just use `HasAttributes` trait and pass key value array to constructor
```php
class Post
{
    use \Clouding\HasAttributes\HasAttributes;
}

$post = new Post([
    'title' => 'Hello',
    'body' => 'World',
]);

echo $post->title; // Hello
echo $post->body;  // World
```

## Installation

```
composer require clouding/has-attributes
```

## Usage

### Define type

Declare `$define` to define type
```php
require 'vendor/autoload.php';

interface Eatable {}

class Pig implements Eatable { }

class Zoo
{
    use \Clouding\HasAttributes\HasAttributes;

    protected $define = [
        'name' => 'string',
        'number' => 'int',
        'animal' => Eatable::class,
    ];
}

$zoo = new Zoo([
    'name' => 'Mike',
    'number' => 100,
    'animal' => new Pig(),
]);

echo $zoo->name;              // Mike
echo $zoo->number;            // 100
echo get_class($zoo->animal); // Pig
```

If you declare `$define` property, it will check type strictly
```php
new Zoo(['name' => 999]);

// InvalidArgumentException: [name => 999] value is not equal to define type [string] 
```

And can't set key that is not defined
```php
new Zoo(['foo' => 'bar']);

// InvalidArgumentException: Key [foo] is not defined 
```

Supported `$define` type:

- `string`
- `int`, `integer`
- `bool`, `boolean`
- `object`
- `array`
- `real`, `float`, `double`
- Class, Interface

### Set attributes

There have many way to set attributes
```php
class Person
{
    use \Clouding\HasAttributes\HasAttributes;
}

$person = new Person([
    'id' => 100,
]);

$person->setAttributes([
    'name' => 'Marry',
    'phone' => '0912345678'
]);

$person->setAttribute('sex', 'female');

$person->age = 18;

echo $person->id;    // 100
echo $person->name;  // Marry
echo $person->phone; // 0912345678
echo $person->sex;   // female
echo $person->age;   // 18
```

### Get attributes
```php
class Person
{
    use \Clouding\HasAttributes\HasAttributes;
}

$person = new Person([
    'id' => 100,
    'name' => 'Jack',
]);

echo $person->getAttribute('id');                 // 100

echo $person->getAttribute('salary', 0);          // 0 (if key not exists return default value)

var_dump($person->getAttributes('id', 'name'));   // ['id' => 100, 'name' => 'Jack']

var_dump($person->getAttributes(['id', 'name'])); // ['id' => 100, 'name' => 'Jack']
```

