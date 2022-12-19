<?php declare(strict_types=1);

namespace BelVG\SepaQr\Hook\Handler;

use BelVG\SepaQr\Hook\AbstractHookHandler;
use Context;
use Order;

class DisplayOrderConfirmation2 extends AbstractHookHandler implements HookHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function execute(array $params)
    {
        /** @var Order|false $order */
        $order = $this->getParam('order', $params);
        if (!$order || !in_array($order->module, $this->modules)) {
            return '';
        }

        $order = new Order($order->id);
        $sepaModel = $this->getSepaModelForOrder($order);

        Context::getContext()->smarty->assign([
            'img' => $sepaModel->getQRImageLink()
        ]);

        return $this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR, 'views/templates/hook/display_order_confirmation.tpl') ?: '';
    }
}