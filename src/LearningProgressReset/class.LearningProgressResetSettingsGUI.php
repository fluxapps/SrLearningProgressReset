<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset;

use ilLink;
use ilSrLearningProgressResetPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class LearningProgressResetSettingsGUI
 *
 * @package           srag\Plugins\SrLearningProgressReset\LearningProgressReset
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLearningProgressReset\LearningProgressReset\LearningProgressResetSettingsGUI: ilUIPluginRouterGUI
 */
class LearningProgressResetSettingsGUI
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;
    const CMD_BACK = "back";
    const CMD_EDIT_SETTINGS = "editSettings";
    const CMD_UPDATE_SETTINGS = "updateSettings";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "learning_progress_reset_settings";
    const TAB_SETTINGS = "settings";
    /**
     * @var int
     */
    protected $obj_ref_id;
    /**
     * @var LearningProgressResetSettings
     */
    protected $learning_progress_reset_settings;


    /**
     * LearningProgressResetSettingsGUI constructor
     */
    public function __construct()
    {

    }


    /**
     *
     */
    public function executeCommand() : void
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID));

        if (!self::srLearningProgressReset()->learningProgressReset()->hasAccess(self::dic()->user()->getId(), $this->obj_ref_id)) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->learning_progress_reset_settings = self::srLearningProgressReset()->learningProgressReset()->getLearningProgressResetSettings($this->obj_ref_id);

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK:
                    case self::CMD_EDIT_SETTINGS:
                    case self::CMD_UPDATE_SETTINGS:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @param int $obj_ref_id
     */
    public static function addTabs(int $obj_ref_id) : void
    {
        if (self::srLearningProgressReset()->learningProgressReset()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()
                ->tabs()
                ->addTab(self::TAB_SETTINGS, self::plugin()->translate("settings", self::LANG_MODULE),
                    self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_EDIT_SETTINGS));
        }
    }


    /**
     *
     */
    protected function setTabs() : void
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget($this->learning_progress_reset_settings->getObject()->getTitle(), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK));

        self::dic()
            ->tabs()
            ->addTab(self::TAB_SETTINGS, self::plugin()->translate("settings", self::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_EDIT_SETTINGS));
    }


    /**
     *
     */
    protected function back() : void
    {
        self::dic()->ctrl()->redirectToURL(ilLink::_getLink($this->obj_ref_id));
    }


    /**
     *
     */
    protected function editSettings() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::srLearningProgressReset()->learningProgressReset()->factory()->newFormBuilderInstance($this, $this->learning_progress_reset_settings);

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function updateSettings() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::srLearningProgressReset()->learningProgressReset()->factory()->newFormBuilderInstance($this, $this->learning_progress_reset_settings);

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_SETTINGS);
    }
}
