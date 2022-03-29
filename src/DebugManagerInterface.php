<?php

declare(strict_types=1);

namespace Pollen\Debug;

use Pollen\Support\Proxy\ContainerProxyInterface;

interface DebugManagerInterface extends ContainerProxyInterface
{
    /**
     * Debug bar instance.
     *
     * @return DebugBarInterface
     */
    public function debugBar(): DebugBarInterface;


    /**
     * Error handler instance.
     *
     * @return ErrorHandlerInterface
     */
    public function errorHandler(): ErrorHandlerInterface;
}