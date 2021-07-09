<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DevTools\SrLearningProgressReset\DevToolsCtrl;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class ilSrLearningProgressResetConfigGUI
 *
 * @ilCtrl_isCalledBy srag\DevTools\SrLearningProgressReset\DevToolsCtrl: ilSrLearningProgressResetConfigGUI
 */
class ilSrLearningProgressResetConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const CMD_CONFIGURE = "configure";
    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;


    /**
     * ilSrLearningProgressResetConfigGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd) : void
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(DevToolsCtrl::class):
                self::dic()->ctrl()->forwardCommand(new DevToolsCtrl($this, self::plugin()));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CONFIGURE:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function configure() : void
    {
        self::dic()->ctrl()->redirectByClass(DevToolsCtrl::class);
    }


    /**
     *
     */
    protected function setTabs() : void
    {
        DevToolsCtrl::addTabs(self::plugin());

        self::dic()->locator()->addItem(ilSrLearningProgressResetPlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }
}
