<?php declare(strict_types=1);

namespace BelVG\SepaQr\Hook\Handler;

use BelVG\SepaQr\Exception\WrongTypeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Module;

class HookHandlerFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    /*public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }*/

    /**
     * @param string $name
     * @param Module $module
     * @return HookHandlerInterface
     * @throws WrongTypeException
     */
    public function create(string $name, Module $module): HookHandlerInterface
    {
        $hookHandlerName = __NAMESPACE__ . '\\' . ucfirst($name);
        if (!class_exists($hookHandlerName)) {
            throw new ServiceNotFoundException($hookHandlerName);
        }
        $hookHandler = new $hookHandlerName($module);
        if (!$hookHandler instanceof HookHandlerInterface) {
            throw new WrongTypeException(sprintf('%hook is not instance of %interface', $hookHandlerName, HookHandlerInterface::class));
        }
        return $hookHandler;
    }
}