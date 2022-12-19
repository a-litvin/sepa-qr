<?php declare(strict_types=1);

namespace BelVG\SepaQr\Hook\Handler;

use BelVG\SepaQr\Hook\AbstractHookHandler;
use Order;

class SendMailAlterTemplateVars extends AbstractHookHandler implements HookHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function execute(array $params)
    {
        $templateVars = $this->getParam('template_vars', $params);
        if (!is_array($templateVars) || !isset($templateVars['{id_order}'])) {
            return;
        }

        $orderId = (int)$templateVars['{id_order}'];
        $order = new Order($orderId);
        if (in_array($order->module, $this->modules)) {
            $sepaModel = $this->getSepaModelForOrder($order);
            $templateVars['{bankwire_sepa_qr}'] = $sepaModel->getQRImageLink();
            $this->updateParam('template_vars', $params, $templateVars);
        }
    }
}