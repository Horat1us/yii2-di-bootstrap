<?php

declare(strict_types=1);

namespace Horat1us\Yii\DI\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Horat1us\Yii\DI\Bootstrap;
use yii\console;
use yii\base;
use yii\di;

#[CoversClass(Bootstrap::class)]
class BootstrapTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \Yii::$container = new di\Container();
    }

    protected function tearDown(): void
    {
        \Yii::$container = new di\Container();
        parent::tearDown();
    }

    public function testInvalidDependency(): void
    {
        $bootstrap = $this->createPartialMock(Bootstrap::class, ['getDefinitions']);
        $bootstrap
            ->expects($this->once())
            ->method('getDefinitions')
            ->willReturn([]);
        $bootstrap->definitions = [
            'invalidDependencyClass' => '',
        ];

        $this->expectException(base\InvalidConfigException::class);
        $this->expectExceptionMessage(
            "Definitions invalidDependencyClass is not supported by " . get_class($bootstrap)
        );

        $bootstrap->init();
    }

    public function testInvalidSingleton(): void
    {
        $bootstrap = $this->createPartialMock(Bootstrap::class, ['getSingletons']);
        $bootstrap
            ->expects($this->once())
            ->method('getSingletons')
            ->willReturn([]);
        $bootstrap->singletons = [
            'invalidSingletonClass' => '',
        ];

        $this->expectException(base\InvalidConfigException::class);
        $this->expectExceptionMessage(
            "Definitions invalidSingletonClass is not supported by " . get_class($bootstrap)
        );

        $bootstrap->init();
    }

    public function testConfigure(): void
    {
        $container = new di\Container();

        $this->createBootstrap()->configure($container);

        $this->assertConfigured($container);
        $this->assertSame([], \Yii::$container->getDefinitions());
    }

    public function testBootstrapConfiguresGivenContainer(): void
    {
        $container = new di\Container();

        $this->createBootstrap()->bootstrap(self::createStub(console\Application::class), $container);

        $this->assertConfigured($container);
        $this->assertSame([], \Yii::$container->getDefinitions());
    }

    public function testBootstrapConfiguresGlobalContainerByDefault(): void
    {
        $this->createBootstrap()->bootstrap(self::createStub(console\Application::class));

        $this->assertConfigured(\Yii::$container);
    }

    public function testBootstrapWithoutDefinitionsConfiguresNothing(): void
    {
        $container = new di\Container();

        (new Bootstrap())->bootstrap(self::createStub(console\Application::class), $container);

        $this->assertSame([], $container->getDefinitions());
        $this->assertSame([], \Yii::$container->getDefinitions());
    }

    /**
     * Defaults: definitions a, b and singletons c, d; configuration overrides definition a and singleton d.
     */
    private function createBootstrap(): Bootstrap
    {
        return new class ([
            'definitions' => ['a' => 'ConfiguredA'],
            'singletons' => ['d' => 'ConfiguredD'],
        ]) extends Bootstrap {
            public function getDefinitions(): array
            {
                return ['a' => 'DefaultA', 'b' => 'DefaultB'];
            }

            public function getSingletons(): array
            {
                return ['c' => 'DefaultC', 'd' => 'DefaultD'];
            }
        };
    }

    private function assertConfigured(di\Container $container): void
    {
        $this->assertSame([
            'a' => ['class' => 'ConfiguredA'],
            'b' => ['class' => 'DefaultB'],
            'c' => ['class' => 'DefaultC'],
            'd' => ['class' => 'ConfiguredD'],
        ], $container->getDefinitions());

        foreach (['a', 'b'] as $definition) {
            $this->assertTrue($container->has($definition), "$definition is defined");
            $this->assertFalse($container->hasSingleton($definition), "$definition is not a singleton");
        }
        foreach (['c', 'd'] as $singleton) {
            $this->assertTrue($container->hasSingleton($singleton), "$singleton is a singleton");
        }
    }
}
