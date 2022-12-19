<?php

use BelVG\SepaQr\Hook\HookManager;
use BelVG\SepaQr\Setup\Installer;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * To Do: add bankwire emails and implement copying of them
 * to the active theme folder
 */
class BelVGSepaQR extends Module
{
    /**
     * @var HookManager
     */
    private $hookManager;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->name = 'belvgsepaqr';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'BelVG';
        parent::__construct();
        $this->displayName = $this->l('Sepa QR for Wire payment');
        $this->description = $this->l('Sepa QR for Wire payment.');
        $this->ps_versions_compliancy = array('min' => '1.7.5.0', 'max' => _PS_VERSION_);

        $this->hookManager = new HookManager();
    }

    /**
     * @inheritDoc
     */
    public function install()
    {
        //$installer = $this->get('BelVG\SepaQr\Setup\Install');
        $installer = new Installer($this->hookManager);
        return parent::install() && $installer->install($this);
    }

    /**
     * @inheritDoc
     */
    public function uninstall()
    {
        $installer = new Installer($this->hookManager);
        return $installer->uninstall() && parent::uninstall();
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->hookManager->process($name, $arguments, $this);
    }
}