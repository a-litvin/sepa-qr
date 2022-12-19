<?php declare(strict_types=1);

namespace BelVG\SepaQr\Model;

use BelVG\SepaQr\Exception\NoSuchEntityException;
use PrestaShopDatabaseException;
use PrestaShopException;

interface SepaModelRepositoryInterface
{
    /**
     * @param int $id
     * @return SepaModel
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws NoSuchEntityException
     */
    public function get(int $id): SepaModel;

    /**
     * @param int $id
     * @return SepaModel
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws NoSuchEntityException
     */
    public function getByOrderId(int $id): SepaModel;

    /**
     * @param SepaModel $sepaQR
     * @return SepaModel
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws NoSuchEntityException
     */
    public function save(SepaModel $sepaQR): SepaModel;

    /**
     * @param SepaModel $sepaQR
     * @return void
     * @throws PrestaShopException
     */
    public function delete(SepaModel $sepaQR): void;
}