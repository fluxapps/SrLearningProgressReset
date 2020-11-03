<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\DI\Container;
use srag\CustomInputGUIs\SrLearningProgressReset\Loader\CustomInputGUIsLoaderDetector;
use srag\DevTools\SrLearningProgressReset\DevToolsCtrl;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;
use srag\RemovePluginDataConfirm\SrLearningProgressReset\PluginUninstallTrait;

/**
 * Class ilSrLearningProgressResetPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilSrLearningProgressResetPlugin extends ilUserInterfaceHookPlugin
{

    use PluginUninstallTrait;
    use SrLearningProgressResetTrait;

    const PLUGIN_CLASS_NAME = self::class;
    const PLUGIN_ID = "srleprre";
    const PLUGIN_NAME = "SrLearningProgressReset";
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * ilSrLearningProgressResetPlugin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @inheritDoc
     */
    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
    }


    /**
     * @inheritDoc
     */
    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null)/* : void*/
    {
        parent::updateLanguages($a_lang_keys);

        $this->installRemovePluginDataConfirmLanguages();

        DevToolsCtrl::installLanguages(self::plugin());
    }


    /**
     * @inheritDoc
     */
    protected function deleteData()/* : void*/
    {
        self::srLearningProgressReset()->dropTables();
    }


    /**
     * @inheritDoc
     */
    protected function shouldUseOneUpdateStepOnly() : bool
    {
        return true;
    }
}
