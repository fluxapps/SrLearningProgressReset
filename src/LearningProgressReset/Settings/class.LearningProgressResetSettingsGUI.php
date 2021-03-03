<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings;

require_once __DIR__ . "/../../../vendor/autoload.php";

use ilLearningProgressGUI;
use ilObjectGUIFactory;
use ilRepositoryGUI;
use ilSrLearningProgressResetPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class LearningProgressResetSettingsGUI
 *
 * @package           srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\LearningProgressResetSettingsGUI: ilUIPluginRouterGUI
 */
class LearningProgressResetSettingsGUI
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const CMD_BACK = "back";
    const CMD_EDIT_SETTINGS = "editSettings";
    const CMD_UPDATE_SETTINGS = "updateSettings";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "learning_progress_reset_settings";
    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;
    const TAB_SETTINGS = "settings";
    /**
     * @var int
     */
    protected $obj_ref_id;
    /**
     * @var Settings
     */
    protected $settings;


    /**
     * LearningProgressResetSettingsGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @param int $obj_ref_id
     */
    public static function addTabs(int $obj_ref_id)/* : void*/
    {
        if (self::srLearningProgressReset()->learningProgressReset()->hasAccess(self::dic()->user()->getId(), $obj_ref_id)) {
            self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);

            self::dic()
                ->tabs()
                ->addSubTab(self::TAB_SETTINGS, self::plugin()->translate("settings", self::LANG_MODULE),
                    self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_EDIT_SETTINGS));
        }
    }


    /**
     *
     */
    public function executeCommand()/* : void*/
    {
        $this->obj_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID));

        if (!self::srLearningProgressReset()->learningProgressReset()->hasAccess(self::dic()->user()->getId(), $this->obj_ref_id)) {
            die();
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->settings = self::srLearningProgressReset()->learningProgressReset()->settings()->getSettings($this->obj_ref_id);

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
     *
     */
    protected function back()/* : void*/
    {
        self::dic()->ctrl()->saveParameterByClass(ilRepositoryGUI::class, self::GET_PARAM_REF_ID);

        self::dic()->ctrl()->redirectToURL(self::dic()->ctrl()->getLinkTargetByClass([
            ilRepositoryGUI::class,
            get_class((new ilObjectGUIFactory())->getInstanceByRefId($this->obj_ref_id)),
            ilLearningProgressGUI::class
        ], "", "", false, false)
        );
    }


    /**
     *
     */
    protected function editSettings()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::srLearningProgressReset()->learningProgressReset()->settings()->factory()->newFormBuilderInstance($this, $this->settings);

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget($this->settings->getObject()->getTitle(), self::dic()->ctrl()->getLinkTarget($this, self::CMD_BACK));

        self::dic()
            ->tabs()
            ->addTab(self::TAB_SETTINGS, self::plugin()->translate("settings", self::LANG_MODULE),
                self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_EDIT_SETTINGS));
    }


    /**
     *
     */
    protected function updateSettings()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::srLearningProgressReset()->learningProgressReset()->settings()->factory()->newFormBuilderInstance($this, $this->settings);

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_SETTINGS);
    }
}
