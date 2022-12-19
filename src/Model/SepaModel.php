<?php declare(strict_types=1);

namespace BelVG\SepaQr\Model;

use BelVG\SepaQr\Model\Config\BankWireConfigProvider;
use BelVG\SepaQr\Model\Config\ConfigProviderInterface;
use BelVG\SepaQr\Model\QrCode\GeneratorInterface;
use BelVG\SepaQr\Model\QrCode\Generator\BaconQrCode;
use Exception;
use Link;
use ObjectModel;
use Order;

class SepaModel extends ObjectModel
{
    const TABLE_NAME = 'sepa_qr';

    const IMG_FOLDER = 'sepa-qr';

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $id_sepa;

    /**
     * @var int
     */
    public $id_order;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $date_add;

    /**
     * @var string
     */
    public $date_upd;

    /**
     * @var GeneratorInterface
     */
    private $qrGenerator;

    /**
     * @var ConfigProviderInterface
     */
    protected $configProvider;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => self::TABLE_NAME,
        'primary' => 'id_sepa',
        'multilang' => false,
        'fields' => [
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'path' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function __construct(
        $id = null,
        $id_lang = null,
        $id_shop = null
    ) {
        parent::__construct($id, $id_lang, $id_shop);
        $this->qrGenerator = new BaconQrCode();
        $this->configProvider = new BankWireConfigProvider();
    }

    /**
     * @param Order $order
     * @param string $filename
     * @return string
     * @throws Exception
     */
    private function getFilePath(Order $order, string $filename): string
    {
        $fileDir = _PS_IMG_DIR_ . SepaModel::IMG_FOLDER;
        if (!is_dir($fileDir) && !mkdir($fileDir)) {
            throw new Exception('Cannot create QR-code images folder.');
        }

        return $fileDir . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * @param Order $order
     * @return string
     */
    private function getFileName(Order $order): string
    {
        return $order->id . '.svg';
    }

    /**
     * To Do: exception handling
     *
     * @param Order $order
     * @return $this
     * @throws Exception
     */
    public function generateQR(Order $order): SepaModel
    {
        try {
            $filename = $this->getFileName($order);
            $filepath = $this->getFilePath($order, $filename);
            $this->qrGenerator->generate($order, $filepath);
            $this->path = $filename;
        } catch (Exception $e) {
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getQRImageLink(): ?string
    {
        $url = $this->path;
        if (null !== $url) {
            $url = _PS_IMG_ . self::IMG_FOLDER . DIRECTORY_SEPARATOR . $url;
            $protocol = ($this->configProvider->get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            $url = (new Link(null, $protocol))->getMediaLink($url);
        }

        return $url;
    }
}