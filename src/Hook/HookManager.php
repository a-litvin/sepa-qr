<?php declare(strict_types=1);

namespace BelVG\SepaQr\Hook;

use BelVG\SepaQr\Hook\Handler\HookHandlerFactory;
use Exception;
use Module;

class HookManager
{
    /**
     * @var string[]
     */
    // private $hooks;
    private $hooks = [
        'displayOrderConfirmation2',
        'sendMailAlterTemplateVars'
    ];

    /**
     * @var HookHandlerFactory
     */
    private $hookHandlerFactory;

    public function __construct()
    {
        $this->hookHandlerFactory = new HookHandlerFactory();
    }

    /**
     * @param Module $module
     * @return bool
     */
    public function registerHooks(Module $module): bool
    {
        $result = true;
        foreach ($this->hooks as $hook) {
            $result &= $module->registerHook($hook);
        }
        return (bool)$result;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @param Module $module
     * @return mixed|void
     */
    public function process(string $name, array $arguments, Module $module)
    {
        $name = str_ireplace('hook', '', $name);
        if (in_array($name, $this->hooks)) {
            try {
                $name = ucfirst($name);
                $hookHandler = $this->hookHandlerFactory->create($name, $module);
                return $hookHandler->execute($arguments);
            } catch (Exception $e) {
                // To Do: add exception handler.
            }
        }
    }
}