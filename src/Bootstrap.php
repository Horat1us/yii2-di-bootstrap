<?php

declare(strict_types=1);

namespace Horat1us\Yii\DI;

use yii\base;
use yii\di;

/**
 * Class Bootstrap
 * @package Horat1us\Yii\DI
 */
abstract class Bootstrap extends base\BaseObject implements base\BootstrapInterface
{
    /** @var string[]|array[]|callable references */
    public $definitions = [];

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        $invalidDependencies = array_diff_key($this->definitions, $this->getDefinitions());
        if ($invalidDependencies) {
            throw new base\InvalidConfigException(
                "Definitions " . implode(", ", array_keys($invalidDependencies))
                . " is not supported by " . static::class
            );
        }
    }

    public function bootstrap($app, di\Container $container = null): void
    {
        $this->configure($container ?? \Yii::$container);
    }

    public function configure(di\Container $container): void
    {
        $definitions = array_merge($this->getDefinitions(), $this->definitions);
        $container->setDefinitions($definitions);
    }

    abstract public function getDefinitions(): array;
}
