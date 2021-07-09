<?php

declare(strict_types=1);

namespace Pollen\Debug;

use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;

class DebugManager implements DebugManagerInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ContainerProxy;

    /**
     * Debug manager main instance.
     * @var DebugManagerInterface|null
     */
    private static ?DebugManagerInterface $instance = null;

    /**
     * Error handler instance.
     * @var object|null
     */
    protected ?object $errorHandler = null;

    /**
     * Debug bar instance.
     * @var DebugBarInterface|null
     */
    protected ?DebugBarInterface $debugBar = null;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }

        $this->boot();
    }

    /**
     * Retrieve debug manager main instance.
     *
     * @return static
     */
    public static function getInstance(): DebugManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if (!$this->isBooted()) {
            $this->setBooted();
        }
    }

    /**
     * @inheritDoc
     */
    public function debugBar(): DebugBarInterface
    {
        if ($this->debugBar === null) {
            $this->debugBar = $this->containerHas(DebugBarInterface::class)
            ? $this->containerGet(DebugBarInterface::class) : new PhpDebugBarDriver($this);
        }

        return $this->debugBar;
    }

    /**
     * @inheritDoc
     */
    public function errorHandler(): ErrorHandlerInterface
    {
        if ($this->errorHandler === null) {
            $this->errorHandler = $this->containerHas(ErrorHandlerInterface::class)
               ? $this->containerGet(ErrorHandlerInterface::class) : new WhoopsErrorHandler($this);
        }
        return $this->errorHandler;
    }
}