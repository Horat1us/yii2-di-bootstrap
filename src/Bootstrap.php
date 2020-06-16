<?php declare(strict_types=1);

namespace Horat1us\Yii\DI;

use yii\base;
use yii\di;

class Bootstrap extends base\BaseObject implements base\BootstrapInterface
{
    /** @var string[]|array[]|callable[] references */
    public array $definitions = [];

    /** @var string[]|array[]|callable[] singleton references */
    public array $singletons = [];

    public function init(): void
    {
        parent::init();
        $this->validateDefinitionsConfiguration($this->definitions, $this->getDefinitions());
        $this->validateDefinitionsConfiguration($this->singletons, $this->getSingletons());
    }

    public function bootstrap($app, di\Container $container = null): void
    {
        $config = [
            'definitions' => array_merge($this->getDefinitions(), $this->definitions),
            'singletons' => array_merge($this->getSingletons(), $this->singletons),
        ];
        $app->setContainer($config);
    }

    public function getDefinitions(): array
    {
        return [];
    }

    public function getSingletons(): array
    {
        return [];
    }

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
