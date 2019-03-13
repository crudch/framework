<?php

namespace Test\Container;

use Crudch\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testGetObject()
    {
        $this->assertInstanceOf(MyClassStd::class, Container::get(MyClassStd::class));
    }

    public function testSet()
    {
        $class = new MyClassStd;
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
            return new MyClassStd;
        });

        $this->assertInstanceOf(MyClassStd::class, Container::get('closure'));
    }

    public function testResolveDependencies()
    {
        $baz = Container::get(Baz::class);
        $this->assertInstanceOf(Baz::class, $baz);
    }

    /**
     * @expectedException \Crudch\Container\ContainerException
     */
    public function testExceptionUnableToInstance()
    {
        $instance = Container::get(Privat::class);
    }

    /**
     * @expectedException \Crudch\Container\ContainerException
     */
    public function testExceptionUnableToInstanceInstance()
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

class MyClassStd extends \StdClass {}
class Foo {}
class DefaultValue{ public $d; public function __construct($d = 'default'){$this->d=$d;}}
class Bar { public function __construct(Foo $foo) {} }
class Baz { public function __construct(Bar $bar) {} }

class Privat { private function __construct() {} }
class InstancePrivate { public function __construct(Privat $privat) {} }