<?php

declare(strict_types=1);

namespace Clouding\HasAttributes\Tests;

use Clouding\HasAttributes\HasAttributes;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use stdClass;

class HasAttributesTest extends TestCase
{
    public function testSetAttribute()
    {
        $class = new class()
        {
            use HasAttributes;
        };
        $class->setAttribute('color', 'red');

        $this->assertEquals(
            ['color' => 'red'],
            $this->makePublic($class, 'attributes')->getValue($class)
        );
    }

    public function testSetAttributes()
    {
        $class = new class()
        {
            use HasAttributes;
        };
        $class->setAttributes([
            'color' => 'red',
            'number' => 10,
            'hello' => 'world',
        ]);

        $this->assertEquals([
            'color' => 'red',
            'number' => 10,
            'hello' => 'world',
        ], $this->makePublic($class, 'attributes')->getValue($class));
    }

    public function testDynamicSetAttribute()
    {
        $class = new class()
        {
            use HasAttributes;
        };
        $class->color = 'red';

        $this->assertEquals(
            ['color' => 'red'],
            $this->makePublic($class, 'attributes')->getValue($class)
        );
    }

    public function testConstructor()
    {
        $class = new class([
            'color' => 'red',
            'number' => 10,
            'hello' => 'world',
        ])
        {
            use HasAttributes;
        };

        $this->assertEquals([
            'color' => 'red',
            'number' => 10,
            'hello' => 'world',
        ], $this->makePublic($class, 'attributes')->getValue($class));
    }

    public function testGetAttribute()
    {
        $class = new class()
        {
            use HasAttributes;
        };

        $this->assertNull($class->getAttribute('color'));
        $this->assertSame('red', $class->getAttribute('color', 'red'));
    }

    public function testGetAttributes()
    {
        $class = new class()
        {
            use HasAttributes;
        };

        $class->setAttribute('color', 'red');
        $class->setAttribute('fruit', 'apple');

        $this->assertEquals(
            $class->getAttributes('color', 'fruit'),
            ['color' => 'red', 'fruit' => 'apple']
        );
        $this->assertEquals(
            $class->getAttributes(['color', 'fruit']),
            ['color' => 'red', 'fruit' => 'apple']
        );
    }

    public function testDynamicGetAttribute()
    {
        $class = new class([
            'color' => 'red',
        ]) {
            use HasAttributes;
        };

        $this->assertNull($class->fruit);
        $this->assertSame('red', $class->color);
    }

    public function testDefineAndSetNotExistsKeyException()
    {
        $class = new class()
        {
            use HasAttributes;

            protected $define = [
                'color' => 'string',
            ];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Key [fruit] is not defined');

        $class->setAttribute('fruit', 'banana');
    }

    public function testDefineAndSetAttributeNoException()
    {
        $class = new class()
        {
            use HasAttributes;

            protected $define = [
                'color' => 'string',
                'number' => 'int',
                'number2' => 'integer',
                'isAdmin' => 'bool',
                'isAdmin2' => 'boolean',
                'posts' => 'object',
                'article' => 'array',
                'score' => 'real',
                'score2' => 'float',
                'score3' => 'double',
                'pig' => Eatable::class,
            ];
        };

        $class->setAttributes([
            'color' => 'red',
            'number' => 123,
            'number2' => 123,
            'isAdmin' => true,
            'isAdmin2' => true,
            'posts' => new stdClass(),
            'article' => ['a', 'b'],
            'score' => 1.0,
            'score2' => 1.0,
            'score3' => 1.0,
            'pig' => new Pig(),
        ]);

        $this->assertTrue(true);
    }

    public function testDefineAndSetCommonTypeException()
    {
        $class = new class()
        {
            use HasAttributes;

            protected $define = [
                'color' => 'string',
            ];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('[color => 123] value is not equal to define type [string]');

        $class->setAttribute('color', 123);
    }

    public function testDefineAndSetObjectNotInstanceOfTypeException()
    {
        $class = new class()
        {
            use HasAttributes;

            protected $define = [
                'pig' => Eatable::class,
            ];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            '[pig => stdClass] class is not instance of define class [Clouding\HasAttributes\Tests\Eatable]'
        );

        $class->setAttribute('pig', new stdClass());
    }

    public function testDefineAndSetValueNotInstanceOfTypeException()
    {
        $class = new class()
        {
            use HasAttributes;

            protected $define = [
                'pig' => Eatable::class,
            ];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            '[pig => eat] value is not instance of define class [Clouding\HasAttributes\Tests\Eatable]'
        );

        $class->setAttribute('pig', 'eat');
    }

    public function testDefineNotSupportedTypeException()
    {
        $class = new class()
        {
            use HasAttributes;

            protected $define = [
                'pig' => 'foo',
            ];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Define type [foo] is not supported');

        $class->setAttribute('pig', 'eat');
    }

    protected function makePublic($obj, $property)
    {
        $reflect = new ReflectionObject($obj);
        $property = $reflect->getProperty($property);
        $property->setAccessible(true);

        return $property;
    }
}

interface Eatable
{

}

class Pig implements Eatable
{

}
