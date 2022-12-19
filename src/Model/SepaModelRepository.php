<?php declare(strict_types=1);

namespace BelVG\SepaQr\Model;

use BelVG\SepaQr\Exception\NoSuchEntityException;
use Db;

class SepaModelRepository implements SepaModelRepositoryInterface
{
    /**
     * @var Db
     */
    private $db;

    /**
     * @var int[]
     */
    private $sepaQrs = [];

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function get(int $id): SepaModel
    {
        if (!isset($this->sepaQrs[$id])) {
            $sepaQr = new SepaModel($id);
            if (null === $sepaQr->id) {
                throw new NoSuchEntityException(sprintf('No such entity with id %id.', $id));
            }
            $this->sepaQrs[$id] = $sepaQr;
        }

        return $this->sepaQrs[$id];
    }

    /**
     * @inheritDoc
     */
    public function getByOrderId(int $id): SepaModel
    {
        $sepaId = (int)$this->db->getValue('
            SELECT `id_sepa` FROM `' . _DB_PREFIX_ . SepaModel::TABLE_NAME . '`
            WHERE `id_order` = ' . $id . ';'
        );
        return $this->get($sepaId);
    }

    /**
     * @inheritDoc
     */
    public function save(SepaModel $sepaQR): SepaModel
    {
        $sepaQR->save();

        $id = (int)$sepaQR->id;
        if (isset($this->sepaQrs[$id])) {
            unset($this->sepaQrs[$id]);
        }
        $this->sepaQrs[$id] = $sepaQR;

        return $this->get($id);
    }

    /**
     * @inheritDoc
     */
    public function delete(SepaModel $sepaQR): void
    {
        $id = $sepaQR->id;
        if (isset($this->sepaQrs[$id])) {
            unset($this->sepaQrs[$id]);
        }
        $sepaQR->delete();
    }
}