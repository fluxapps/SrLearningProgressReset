<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Form;

use ilSrLearningProgressResetPlugin;
use srag\CustomInputGUIs\SrLearningProgressReset\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrLearningProgressReset\PropertyFormGUI\Items\Items;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\LearningProgressResetSettings;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\LearningProgressResetSettingsGUI;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use SrLearningProgressResetTrait;

    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;
    /**
     * @var LearningProgressResetSettings
     */
    protected $learning_progress_reset_settings;


    /**
     * @inheritDoc
     *
     * @param LearningProgressResetSettingsGUI $parent
     * @param LearningProgressResetSettings    $learning_progress_reset_settings
     */
    public function __construct(LearningProgressResetSettingsGUI $parent, LearningProgressResetSettings $learning_progress_reset_settings)
    {
        $this->learning_progress_reset_settings = $learning_progress_reset_settings;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            LearningProgressResetSettingsGUI::CMD_UPDATE_SETTINGS => self::plugin()->translate("save", LearningProgressResetSettingsGUI::LANG_MODULE)
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [];

        foreach (array_keys($this->getFields()) as $key) {
            $data[$key] = Items::getter($this->learning_progress_reset_settings, $key);
        }

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "days"      => self::dic()->ui()->factory()->input()->field()->numeric(self::plugin()->translate("days", LearningProgressResetSettingsGUI::LANG_MODULE),
                self::plugin()->translate("days_info", LearningProgressResetSettingsGUI::LANG_MODULE))->withRequired(true),
            "udf_field" => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate("udf_field", LearningProgressResetSettingsGUI::LANG_MODULE),
                self::plugin()->translate("udf_field_info", LearningProgressResetSettingsGUI::LANG_MODULE, ["YYYY-MM-DD"]))->withRequired(true)
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("settings", LearningProgressResetSettingsGUI::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data) : void
    {
        foreach (array_keys($this->getFields()) as $key) {
            Items::setter($this->learning_progress_reset_settings, $key, $data[$key]);
        }

        self::srLearningProgressReset()->learningProgressReset()->storeLearningProgressResetSettings($this->learning_progress_reset_settings);
    }
}
