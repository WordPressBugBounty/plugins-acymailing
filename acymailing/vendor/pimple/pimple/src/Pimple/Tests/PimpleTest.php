<?php


namespace Pimple\Tests;

use PHPUnit\Framework\TestCase;
use Pimple\Container;

class PimpleTest extends TestCase
{
    public function testWithString()
    {
        $pimple = new Container();
        $pimple['param'] = 'value';

        $this->assertEquals('value', $pimple['param']);
    }

    public function testWithClosure()
    {
        $pimple = new Container();
        $pimple['service'] = function () {
            return new Fixtures\Service();
        };

        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $pimple['service']);
    }

    public function testServicesShouldBeDifferent()
    {
        $pimple = new Container();
        $pimple['service'] = $pimple->factory(function () {
            return new Fixtures\Service();
        });

        $serviceOne = $pimple['service'];
        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $serviceOne);

        $serviceTwo = $pimple['service'];
        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $serviceTwo);

        $this->assertNotSame($serviceOne, $serviceTwo);
    }

    public function testShouldPassContainerAsParameter()
    {
        $pimple = new Container();
        $pimple['service'] = function () {
            return new Fixtures\Service();
        };
        $pimple['container'] = function ($container) {
            return $container;
        };

        $this->assertNotSame($pimple, $pimple['service']);
        $this->assertSame($pimple, $pimple['container']);
    }

    public function testIsset()
    {
        $pimple = new Container();
        $pimple['param'] = 'value';
        $pimple['service'] = function () {
            return new Fixtures\Service();
        };

        $pimple['null'] = null;

        $this->assertTrue(isset($pimple['param']));
        $this->assertTrue(isset($pimple['service']));
        $this->assertTrue(isset($pimple['null']));
        $this->assertFalse(isset($pimple['non_existent']));
    }

    public function testConstructorInjection()
    {
        $params = ['param' => 'value'];
        $pimple = new Container($params);

        $this->assertSame($params['param'], $pimple['param']);
    }

    public function testOffsetGetValidatesKeyIsPresent()
    {
        $this->expectException(\Pimple\Exception\UnknownIdentifierException::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $pimple = new Container();
        echo $pimple['foo'];
    }

    public function testLegacyOffsetGetValidatesKeyIsPresent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $pimple = new Container();
        echo $pimple['foo'];
    }

    public function testOffsetGetHonorsNullValues()
    {
        $pimple = new Container();
        $pimple['foo'] = null;
        $this->assertNull($pimple['foo']);
    }

    public function testUnset()
    {
        $pimple = new Container();
        $pimple['param'] = 'value';
        $pimple['service'] = function () {
            return new Fixtures\Service();
        };

        unset($pimple['param'], $pimple['service']);
        $this->assertFalse(isset($pimple['param']));
        $this->assertFalse(isset($pimple['service']));
    }

    public function testShare($service)
    {
        $pimple = new Container();
        $pimple['shared_service'] = $service;

        $serviceOne = $pimple['shared_service'];
        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $serviceOne);

        $serviceTwo = $pimple['shared_service'];
        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $serviceTwo);

        $this->assertSame($serviceOne, $serviceTwo);
    }

    public function testProtect($service)
    {
        $pimple = new Container();
        $pimple['protected'] = $pimple->protect($service);

        $this->assertSame($service, $pimple['protected']);
    }

    public function testGlobalFunctionNameAsParameterValue()
    {
        $pimple = new Container();
        $pimple['global_function'] = 'strlen';
        $this->assertSame('strlen', $pimple['global_function']);
    }

    public function testRaw()
    {
        $pimple = new Container();
        $pimple['service'] = $definition = $pimple->factory(function () {
            return 'foo';
        });
        $this->assertSame($definition, $pimple->raw('service'));
    }

    public function testRawHonorsNullValues()
    {
        $pimple = new Container();
        $pimple['foo'] = null;
        $this->assertNull($pimple->raw('foo'));
    }

    public function testFluentRegister()
    {
        $pimple = new Container();
        $this->assertSame($pimple, $pimple->register($this->getMockBuilder('Pimple\ServiceProviderInterface')->getMock()));
    }

    public function testRawValidatesKeyIsPresent()
    {
        $this->expectException(\Pimple\Exception\UnknownIdentifierException::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $pimple = new Container();
        $pimple->raw('foo');
    }

    public function testLegacyRawValidatesKeyIsPresent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $pimple = new Container();
        $pimple->raw('foo');
    }

    public function testExtend($service)
    {
        $pimple = new Container();
        $pimple['shared_service'] = function () {
            return new Fixtures\Service();
        };
        $pimple['factory_service'] = $pimple->factory(function () {
            return new Fixtures\Service();
        });

        $pimple->extend('shared_service', $service);
        $serviceOne = $pimple['shared_service'];
        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $serviceOne);
        $serviceTwo = $pimple['shared_service'];
        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $serviceTwo);
        $this->assertSame($serviceOne, $serviceTwo);
        $this->assertSame($serviceOne->value, $serviceTwo->value);

        $pimple->extend('factory_service', $service);
        $serviceOne = $pimple['factory_service'];
        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $serviceOne);
        $serviceTwo = $pimple['factory_service'];
        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $serviceTwo);
        $this->assertNotSame($serviceOne, $serviceTwo);
        $this->assertNotSame($serviceOne->value, $serviceTwo->value);
    }

    public function testExtendDoesNotLeakWithFactories()
    {
        if (\extension_loaded('pimple')) {
            $this->markTestSkipped('Pimple extension does not support this test');
        }
        $pimple = new Container();

        $pimple['foo'] = $pimple->factory(function () {
            return;
        });
        $pimple['foo'] = $pimple->extend('foo', function ($foo, $pimple) {
            return;
        });
        unset($pimple['foo']);

        $p = new \ReflectionProperty($pimple, 'values');
        $p->setAccessible(true);
        $this->assertEmpty($p->getValue($pimple));

        $p = new \ReflectionProperty($pimple, 'factories');
        $p->setAccessible(true);
        $this->assertCount(0, $p->getValue($pimple));
    }

    public function testExtendValidatesKeyIsPresent()
    {
        $this->expectException(\Pimple\Exception\UnknownIdentifierException::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $pimple = new Container();
        $pimple->extend('foo', function () {
        });
    }

    public function testLegacyExtendValidatesKeyIsPresent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier "foo" is not defined.');

        $pimple = new Container();
        $pimple->extend('foo', function () {
        });
    }

    public function testKeys()
    {
        $pimple = new Container();
        $pimple['foo'] = 123;
        $pimple['bar'] = 123;

        $this->assertEquals(['foo', 'bar'], $pimple->keys());
    }

    public function settingAnInvokableObjectShouldTreatItAsFactory()
    {
        $pimple = new Container();
        $pimple['invokable'] = new Fixtures\Invokable();

        $this->assertInstanceOf('Pimple\Tests\Fixtures\Service', $pimple['invokable']);
    }

    public function settingNonInvokableObjectShouldTreatItAsParameter()
    {
        $pimple = new Container();
        $pimple['non_invokable'] = new Fixtures\NonInvokable();

        $this->assertInstanceOf('Pimple\Tests\Fixtures\NonInvokable', $pimple['non_invokable']);
    }

    public function testFactoryFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(\Pimple\Exception\ExpectedInvokableException::class);
        $this->expectExceptionMessage('Service definition is not a Closure or invokable object.');

        $pimple = new Container();
        $pimple->factory($service);
    }

    public function testLegacyFactoryFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Service definition is not a Closure or invokable object.');

        $pimple = new Container();
        $pimple->factory($service);
    }

    public function testProtectFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(\Pimple\Exception\ExpectedInvokableException::class);
        $this->expectExceptionMessage('Callable is not a Closure or invokable object.');

        $pimple = new Container();
        $pimple->protect($service);
    }

    public function testLegacyProtectFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Callable is not a Closure or invokable object.');

        $pimple = new Container();
        $pimple->protect($service);
    }

    public function testExtendFailsForKeysNotContainingServiceDefinitions($service)
    {
        $this->expectException(\Pimple\Exception\InvalidServiceIdentifierException::class);
        $this->expectExceptionMessage('Identifier "foo" does not contain an object definition.');

        $pimple = new Container();
        $pimple['foo'] = $service;
        $pimple->extend('foo', function () {
        });
    }

    public function testLegacyExtendFailsForKeysNotContainingServiceDefinitions($service)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier "foo" does not contain an object definition.');

        $pimple = new Container();
        $pimple['foo'] = $service;
        $pimple->extend('foo', function () {
        });
    }

    public function testExtendingProtectedClosureDeprecation()
    {
        $pimple = new Container();
        $pimple['foo'] = $pimple->protect(function () {
            return 'bar';
        });

        $pimple->extend('foo', function ($value) {
            return $value.'-baz';
        });

        $this->assertSame('bar-baz', $pimple['foo']);
    }

    public function testExtendFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(\Pimple\Exception\ExpectedInvokableException::class);
        $this->expectExceptionMessage('Extension service definition is not a Closure or invokable object.');

        $pimple = new Container();
        $pimple['foo'] = function () {
        };
        $pimple->extend('foo', $service);
    }

    public function testLegacyExtendFailsForInvalidServiceDefinitions($service)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Extension service definition is not a Closure or invokable object.');

        $pimple = new Container();
        $pimple['foo'] = function () {
        };
        $pimple->extend('foo', $service);
    }

    public function testExtendFailsIfFrozenServiceIsNonInvokable()
    {
        $this->expectException(\Pimple\Exception\FrozenServiceException::class);
        $this->expectExceptionMessage('Cannot override frozen service "foo".');

        $pimple = new Container();
        $pimple['foo'] = function () {
            return new Fixtures\NonInvokable();
        };
        $foo = $pimple['foo'];

        $pimple->extend('foo', function () {
        });
    }

    public function testExtendFailsIfFrozenServiceIsInvokable()
    {
        $this->expectException(\Pimple\Exception\FrozenServiceException::class);
        $this->expectExceptionMessage('Cannot override frozen service "foo".');

        $pimple = new Container();
        $pimple['foo'] = function () {
            return new Fixtures\Invokable();
        };
        $foo = $pimple['foo'];

        $pimple->extend('foo', function () {
        });
    }

    public function badServiceDefinitionProvider()
    {
        return [
          [123],
          [new Fixtures\NonInvokable()],
        ];
    }

    public function serviceDefinitionProvider()
    {
        return [
            [function ($value) {
                $service = new Fixtures\Service();
                $service->value = $value;

                return $service;
            }],
            [new Fixtures\Invokable()],
        ];
    }

    public function testDefiningNewServiceAfterFreeze()
    {
        $pimple = new Container();
        $pimple['foo'] = function () {
            return 'foo';
        };
        $foo = $pimple['foo'];

        $pimple['bar'] = function () {
            return 'bar';
        };
        $this->assertSame('bar', $pimple['bar']);
    }

    public function testOverridingServiceAfterFreeze()
    {
        $this->expectException(\Pimple\Exception\FrozenServiceException::class);
        $this->expectExceptionMessage('Cannot override frozen service "foo".');

        $pimple = new Container();
        $pimple['foo'] = function () {
            return 'foo';
        };
        $foo = $pimple['foo'];

        $pimple['foo'] = function () {
            return 'bar';
        };
    }

    public function testLegacyOverridingServiceAfterFreeze()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot override frozen service "foo".');

        $pimple = new Container();
        $pimple['foo'] = function () {
            return 'foo';
        };
        $foo = $pimple['foo'];

        $pimple['foo'] = function () {
            return 'bar';
        };
    }

    public function testRemovingServiceAfterFreeze()
    {
        $pimple = new Container();
        $pimple['foo'] = function () {
            return 'foo';
        };
        $foo = $pimple['foo'];

        unset($pimple['foo']);
        $pimple['foo'] = function () {
            return 'bar';
        };
        $this->assertSame('bar', $pimple['foo']);
    }

    public function testExtendingService()
    {
        $pimple = new Container();
        $pimple['foo'] = function () {
            return 'foo';
        };
        $pimple['foo'] = $pimple->extend('foo', function ($foo, $app) {
            return "$foo.bar";
        });
        $pimple['foo'] = $pimple->extend('foo', function ($foo, $app) {
            return "$foo.baz";
        });
        $this->assertSame('foo.bar.baz', $pimple['foo']);
    }

    public function testExtendingServiceAfterOtherServiceFreeze()
    {
        $pimple = new Container();
        $pimple['foo'] = function () {
            return 'foo';
        };
        $pimple['bar'] = function () {
            return 'bar';
        };
        $foo = $pimple['foo'];

        $pimple['bar'] = $pimple->extend('bar', function ($bar, $app) {
            return "$bar.baz";
        });
        $this->assertSame('bar.baz', $pimple['bar']);
    }
}
