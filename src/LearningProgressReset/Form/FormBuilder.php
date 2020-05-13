<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Form;

use ilSrLearningProgressResetPlugin;
use ilUserDefinedFields;
use srag\CustomInputGUIs\SrLearningProgressReset\FormBuilder\AbstractFormBuilder;
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
        if (self::version()->is6()) {
            $data = [
                "enabled" => ($this->learning_progress_reset_settings->isEnabled() ? [
                    "days"      => $this->learning_progress_reset_settings->getDays(),
                    "udf_field" => $this->learning_progress_reset_settings->getUdfField()
                ] : null)
            ];
        } else {
            $data = [
                "enabled" => [
                    "value"        => $this->learning_progress_reset_settings->isEnabled(),
                    "group_values" => [
                        "dependant_group" => [
                            "days"      => $this->learning_progress_reset_settings->getDays(),
                            "udf_field" => $this->learning_progress_reset_settings->getUdfField()
                        ]
                    ]
                ]
            ];
        }

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "enabled" => self::dic()->ui()->factory()->input()->field()->optionalGroup([
                "days"      => self::dic()->ui()->factory()->input()->field()->numeric(self::plugin()->translate("days", LearningProgressResetSettingsGUI::LANG_MODULE),
                    self::plugin()->translate("days_info", LearningProgressResetSettingsGUI::LANG_MODULE))->withRequired(true),
                "udf_field" => self::dic()->ui()->factory()->input()->field()->select(self::plugin()->translate("udf_field", LearningProgressResetSettingsGUI::LANG_MODULE),
                    [0 => ""] + array_map(function (array $field) : string {
                        return $field["field_name"];
                    }, ilUserDefinedFields::_getInstance()->getDefinitions()),
                    self::plugin()->translate("udf_field_info", LearningProgressResetSettingsGUI::LANG_MODULE, [LearningProgressResetSettings::DATE_FORMAT]))->withRequired(true)
            ], self::plugin()->translate("enabled", LearningProgressResetSettingsGUI::LANG_MODULE)),
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
    protected function storeData(array $data)/* : void*/
    {
        if (self::version()->is6()) {
            if (!empty($data["enabled"])) {
                $this->learning_progress_reset_settings->setEnabled(true);
                $this->learning_progress_reset_settings->setDays(intval($data["enabled"]["days"]));
                $this->learning_progress_reset_settings->setUdfField(intval($data["enabled"]["udf_field"]));
            } else {
                $this->learning_progress_reset_settings->setEnabled(false);
            }
        } else {
            $this->learning_progress_reset_settings->setEnabled(boolval($data["enabled"]["value"]));
            $this->learning_progress_reset_settings->setDays(intval(($this->learning_progress_reset_settings->isEnabled() ? $data["enabled"]["group_values"]
                : $data["enabled"])["dependant_group"]["days"]));
            $this->learning_progress_reset_settings->setUdfField(intval(($this->learning_progress_reset_settings->isEnabled() ? $data["enabled"]["group_values"]
                : $data["enabled"])["dependant_group"]["udf_field"]));
        }

        self::srLearningProgressReset()->learningProgressReset()->storeLearningProgressResetSettings($this->learning_progress_reset_settings);
    }
}
