<?php

declare(strict_types=1);

namespace Horat1us\Yii\DI;

use yii\base;
use yii\di;

/**
 * Class Bootstrap
 * @package Horat1us\Yii\DI
 */
class Bootstrap extends base\BaseObject implements base\BootstrapInterface
{
    /** @var string[]|array[]|callable references */
    public $definitions = [];

    /** @var string[]|array[]|callable singleton references */
    public $singletons = [];

    /**
     * @throws base\InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->validateDefinitionsConfiguration($this->definitions, $this->getDefinitions());
        $this->validateDefinitionsConfiguration($this->singletons, $this->getSingletons());
    }

    public function bootstrap($app, di\Container $container = null): void
    {
        $this->configure($container ?? \Yii::$container);
    }

    public function configure(di\Container $container): void
    {
        $definitions = array_merge($this->getDefinitions(), $this->definitions);
        $container->setDefinitions($definitions);
        $singletons = array_merge($this->getSingletons(), $this->singletons);
        $container->setSingletons($singletons);
    }

    public function getDefinitions(): array
    {
        return [];
    }

    public function getSingletons(): array
    {
        return [];
    }

    /**
     * @throws base\InvalidConfigException
     */
    protected function validateDefinitionsConfiguration(array $configured, array $example): void
    {
        $invalidDependencies = array_diff_key($configured, $example);
        if ($invalidDependencies) {
            throw new base\InvalidConfigException(
                "Definitions " . implode(", ", array_keys($invalidDependencies))
                . " is not supported by " . static::class
            );
        }
    }
}
