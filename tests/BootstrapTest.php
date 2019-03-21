<?php

declare(strict_types=1);

namespace Horat1us\Yii\DI\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Horat1us\Yii\DI;
use yii\base;

/**
 * Class BootstrapTest
 * @package Horat1us\Yii\DI\Tests
 */
class BootstrapTest extends TestCase
{
    public function testInvalidDependency(): void
    {
        /** @var DI\Bootstrap|MockObject $bootstrap */
        /** @noinspection PhpUnhandledExceptionInspection */
        $bootstrap = $this->getMockBuilder(DI\Bootstrap::class)
            ->setMethods(['getDefinitions'])
            ->disableOriginalConstructor()
            ->setMockClassName('BootstrapMock')
            ->getMockForAbstractClass();

        $bootstrap
            ->expects($this->once())
            ->method('getDefinitions')
            ->willReturn([]);

        $bootstrap->definitions = [
            'invalidDependencyClass' => '',
        ];

        $this->expectException(base\InvalidConfigException::class);
        $this->expectExceptionMessage("Definitions invalidDependencyClass is not supported by BootstrapMock");

        /** @noinspection PhpUnhandledExceptionInspection */
        $bootstrap->init();
    }

    public function testBootstrap(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $container = $this->createMock(\yii\di\Container::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @var \yii\di\Container $container */
        $app = $this->getMockBuilder(base\Application::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        /** @var DI\Bootstrap|MockObject $bootstrap */
        /** @noinspection PhpUnhandledExceptionInspection */
        $bootstrap = $this->getMockBuilder(DI\Bootstrap::class)
            ->setMethods(['configure'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $bootstrap
            ->expects($this->once())
            ->method('configure')
            ->with($container);

        /** @noinspection PhpUnhandledExceptionInspection */
        $bootstrap->init();
        $bootstrap->bootstrap($app, $container);
    }

    public function testConfigure(): void
    {

        /** @var DI\Bootstrap|MockObject $bootstrap */
        /** @noinspection PhpUnhandledExceptionInspection */
        $bootstrap = $this->getMockBuilder(DI\Bootstrap::class)
            ->setMethods(['getDefinitions'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $bootstrap
            ->expects($this->once())
            ->method('getDefinitions')
            ->willReturn($default = ['a' => 'default', 'b' => 'default']);

        $bootstrap->definitions = ['a' => 'overriden', 'd' => 'added',];

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @var \yii\di\Container|MockObject $container */
        $container = $this->createMock(\yii\di\Container::class);
        $container
            ->expects($this->once())
            ->method('setDefinitions')
            ->with([
                'a' => 'overriden',
                'b' => 'default',
                'd' => 'added',
            ]);

        $bootstrap->configure($container);
    }
}
