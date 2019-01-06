<?php

use Crudch\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testGetObject()
    {
        $this->assertInstanceOf(StdClass::class, Container::get(StdClass::class));
    }

    public function testSet()
    {
        $class = new StdClass();
        Container::set('std', $class);
        $this->assertEquals($class, Container::get('std'));
        Container::set('std', $class);
        $this->assertEquals($class, Container::get('std'));
    }

    /**
     * @expectedException \Crudch\Container\ContainerException
     */
    public function testNotExistsClassInContainer()
    {
        Container::get('NotExists');
    }

    public function testSetClosure()
    {
        Container::set('closure', function () {
            return new StdClass();
        });

        $this->assertInstanceOf(StdClass::class, Container::get('closure'));
    }

    public function testResolveDependencies()
    {
        $baz = Container::get(Baz::class);
        $this->assertInstanceOf('Baz', $baz);
    }

    /**
     * @expectedException \Crudch\Container\ContainerException
     */
    public function testExeptionUnableToInstance()
    {
        $instance = Container::get(Privat::class);
    }

    /**
     * @expectedException \Crudch\Container\ContainerException
     */
    public function testExeptionUnableToInstanceInstance()
    {
        $instance = Container::get(InstancePrivate::class);
    }

    public function testEqualsContainerClasses()
    {
        Container::set('foo', function () {
            return new Foo();
        });

        $foo = Container::get('foo');

        $this->assertSame($foo, Container::get('foo'));
    }

    public function testConstructWithDefaultValue()
    {
        $default = Container::get(DefaultValue::class);

        $this->assertSame($default->d, 'default');
    }
}

class Foo {}
class DefaultValue{ public $d; public function __construct($d = 'default'){$this->d=$d;}}
class Bar { public function __construct(Foo $foo) {} }
class Baz { public function __construct(Bar $bar) {} }

class Privat { private function __construct() {} }
class InstancePrivate { public function __construct(Privat $privat) {} }