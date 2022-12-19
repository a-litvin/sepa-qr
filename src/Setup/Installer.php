<?php declare(strict_types=1);

namespace BelVG\SepaQr\Setup;

use BelVG\SepaQr\Hook\HookManager;
use BelVG\SepaQr\Model\SepaModel;
use Db;
use Module;

class Installer
{
    /**
     * @var HookManager
     */
    private $hookManager;

    /**
     * Class constructor
     *
     * @param HookManager $hookManager
     */
    public function __construct(HookManager $hookManager)
    {
        $this->hookManager = $hookManager;
    }

    /**
     * @param Module $module
     * @return bool
     */
    public function install(Module $module): bool
    {
        return $this->hookManager->registerHooks($module)
            && $this->installDirs()
            && $this->installDB();
    }

    /**
     * @return bool
     */
    protected function installDirs(): bool
    {
        if (!is_dir(_PS_IMG_DIR_ . SepaModel::IMG_FOLDER)) {
            return mkdir(_PS_IMG_DIR_ . SepaModel::IMG_FOLDER);
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function installDB(): bool
    {
        return Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . SepaModel::TABLE_NAME . '
                (
                    `id_sepa`    INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
                    `id_order`      INT(10) UNSIGNED    NOT NULL UNIQUE,
                    `path`          VARCHAR(128)        NULL,
                    `date_add`      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `date_upd`      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id_sepa)
                ) ENGINE = ' . _MYSQL_ENGINE_ . '
            DEFAULT CHARSET = UTF8;
        ');
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return $this->uninstallDirs() && $this->uninstallDB();
    }

    /**
     * @return bool
     */
    protected function uninstallDirs(): bool
    {
        if (is_dir(_PS_IMG_DIR_ . SepaModel::IMG_FOLDER)) {
            return rmdir(_PS_IMG_DIR_ . SepaModel::IMG_FOLDER);
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function uninstallDB(): bool
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS ' . _DB_PREFIX_ . SepaModel::TABLE_NAME . ';
        ');
    }
}