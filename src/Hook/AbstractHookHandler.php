<?php declare(strict_types=1);

namespace BelVG\SepaQr\Hook;

use BelVG\SepaQr\Exception\NoSuchEntityException;
use BelVG\SepaQr\Model\SepaModel;
use BelVG\SepaQr\Model\SepaModelRepository;
use Module;
use Order;
use SepaQr\Exception;

abstract class AbstractHookHandler
{
    /**
     * @var SepaModelRepository
     */
    protected $sepaModelRepository;

    /**
     * @var Module
     */
    protected $module;

    /**
     * @var string[]
     */
    protected $modules = ['ps_wirepayment'];

    /**
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->sepaModelRepository = new SepaModelRepository();
        $this->module = $module;
    }

    /**
     * @param string $name
     * @param array $inputParams
     * @return false|mixed
     */
    protected function getParam(string $name, array $inputParams)
    {
        $params = array_shift($inputParams);
        return $params[$name] ?: false;
    }

    /**
     * @param string $name
     * @param array $inputParams
     * @param mixed $value
     * @return void
     */
    protected function updateParam(string $name, array $inputParams, $value): void
    {
        $params = array_shift($inputParams);
        if (isset($params[$name])) {
            $params[$name] = $value;
        }
        array_unshift($inputParams, $params);
    }

    /**
     * @param Order $order
     * @return SepaModel
     * @throws NoSuchEntityException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws Exception
     */
    protected function getSepaModelForOrder(Order $order): SepaModel
    {
        try {
            $sepaModel = $this->sepaModelRepository->getByOrderId((int)$order->id);
        } catch (NoSuchEntityException $e) {
            $sepaModel = new SepaModel();
            $sepaModel->id_order = $order->id;
            $sepaModel->generateQR($order);
            $this->sepaModelRepository->save($sepaModel);
        }

        return $sepaModel;
    }
}