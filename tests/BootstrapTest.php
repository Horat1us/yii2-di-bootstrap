<?php

declare(strict_types=1);

namespace Horat1us\Yii\DI\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Horat1us\Yii\DI\Bootstrap;
use yii\console;
use yii\base;
use yii\di;

class BootstrapTest extends TestCase
{
    private Bootstrap $bootstrap;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootstrap = $this->createPartialMock(Bootstrap::class, ['getDefinitions']);
    }

    public function testInvalidDependency(): void
    {
        $this->bootstrap
            ->expects($this->once())
            ->method('getDefinitions')
            ->willReturn([]);
        $this->bootstrap->definitions = [
            'invalidDependencyClass' => '',
        ];

        $this->expectException(base\InvalidConfigException::class);
        $this->expectExceptionMessage(
            "Definitions invalidDependencyClass is not supported by " . get_class($this->bootstrap)
        );

        $this->bootstrap->init();
    }

    public function testBootstrap(): void
    {
        $this->bootstrap
            ->expects($this->once())
            ->method('getDefinitions')
            ->willReturn($default = ['a' => 'default', 'b' => 'default']);
        $this->bootstrap->definitions = ['a' => 'overriden', 'd' => 'added',];

        $app = $this->createMock(console\Application::class);
        $app->expects($this->once())->method('setContainer')->with(['definitions' => [
            'a' => 'overriden',
            'b' => 'default',
            'd' => 'added',
        ], 'singletons' => [],]);

        $this->bootstrap->bootstrap($app);
    }
}
