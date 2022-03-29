<?php

declare(strict_types=1);

namespace Pollen\Debug;

use Pollen\Container\BootableServiceProvider;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Kernel\Events\ConfigLoadEvent;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DebugServiceProvider extends BootableServiceProvider
{
    protected $provides = [
        DebugManagerInterface::class,
        DebugBarInterface::class,
        ErrorHandlerInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        try {
            /** @var EventDispatcherInterface $event */
            if ($event = $this->getContainer()->get(EventDispatcherInterface::class)) {
                $event->subscribeTo('config.load', static function (ConfigLoadEvent $event) {
                    if (is_callable($config = $event->getConfig('debug'))) {
                        $config($event->getApp()->get(DebugManagerInterface::class), $event->getApp());
                    }
                });
            }
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            unset($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            DebugManagerInterface::class,
            function () {
                return new DebugManager($this->getContainer());
            }
        );

        $this->getContainer()->add(
            DebugBarInterface::class,
            function () {
                return new PhpDebugBarDriver($this->getContainer()->get(DebugManagerInterface::class));
            }
        );

        $this->getContainer()->share(
            ErrorHandlerInterface::class,
            function () {
                return new WhoopsErrorHandler($this->getContainer()->get(DebugManagerInterface::class));
            }
        );
    }
}