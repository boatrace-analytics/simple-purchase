<?php

namespace Boatrace\Analytics;

use DI\Container;
use DI\ContainerBuilder;

/**
 * @author shimomo
 */
class SimplePurchaser
{
    /**
     * @var \Boatrace\Analytics\MainSimplePurchaser
     */
    protected $purchaser;

    /**
     * @var \Boatrace\Analytics\SimplePurchaser
     */
    protected static $instance;

    /**
     * @var \DI\Container
     */
    protected static $container;

    /**
     * @param  \Boatrace\Analytics\MainSimplePurchaser  $purchaser
     * @return void
     */
    public function __construct(MainSimplePurchaser $purchaser)
    {
        $this->purchaser = $purchaser;
    }

    /**
     * @param  string  $name
     * @param  array   $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->purchaser, $name], $arguments);
    }

    /**
     * @param  string  $name
     * @param  array   $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return call_user_func_array([self::getInstance(), $name], $arguments);
    }

    /**
     * @return \Boatrace\Analytics\SimplePurchaser
     */
    public static function getInstance(): SimplePurchaser
    {
        return self::$instance ?? self::$instance = (
            self::$container ?? self::$container = self::getContainer()
        )->get('SimplePurchaser');
    }

    /**
     * @return \DI\Container
     */
    public static function getContainer(): Container
    {
        $builder = new ContainerBuilder;

        $builder->addDefinitions(__DIR__ . '/../config/definitions.php');

        return $builder->build();
    }
}
