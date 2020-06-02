<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Form;

use ilSrLearningProgressResetPlugin;
use ilUserDefinedFields;
use srag\CustomInputGUIs\SrLearningProgressReset\FormBuilder\AbstractFormBuilder;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\LearningProgressResetSettingsGUI;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings;
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
     * @var Settings
     */
    protected $settings;


    /**
     * @inheritDoc
     *
     * @param LearningProgressResetSettingsGUI $parent
     * @param Settings                         $settings
     */
    public function __construct(LearningProgressResetSettingsGUI $parent, Settings $settings)
    {
        $this->settings = $settings;

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
        $data = [
            "enabled" => [
                "value"        => $this->settings->isEnabled(),
                "group_values" => [
                    "dependant_group" => [
                        "days"      => $this->settings->getDays(),
                        "udf_field" => $this->settings->getUdfField()
                    ]
                ]
            ]
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $enabled_fields = [
            "days"      => self::dic()->ui()->factory()->input()->field()->numeric(self::plugin()->translate("days", LearningProgressResetSettingsGUI::LANG_MODULE),
                self::plugin()->translate("days_info", LearningProgressResetSettingsGUI::LANG_MODULE))->withRequired(true),
            "udf_field" => self::dic()->ui()->factory()->input()->field()->select(self::plugin()->translate("udf_field", LearningProgressResetSettingsGUI::LANG_MODULE),
                [0 => ""] + array_map(function (array $field) : string {
                    return $field["field_name"];
                }, ilUserDefinedFields::_getInstance()->getDefinitions()),
                self::plugin()->translate("udf_field_info", LearningProgressResetSettingsGUI::LANG_MODULE, [Settings::DATE_FORMAT]))->withRequired(true)
        ];

        if (self::version()->is6()) {
            $fields = [
                "enabled" => self::dic()->ui()->factory()->input()->field()->optionalGroup($enabled_fields, self::plugin()->translate("enabled", LearningProgressResetSettingsGUI::LANG_MODULE))
            ];
        } else {
            $fields = [
                "enabled" => self::dic()
                    ->ui()
                    ->factory()
                    ->input()
                    ->field()
                    ->checkbox(self::plugin()->translate("enabled", LearningProgressResetSettingsGUI::LANG_MODULE))
                    ->withDependantGroup(self::dic()->ui()->factory()->input()->field()->dependantGroup($enabled_fields))
            ];
        }

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
                $this->settings->setEnabled(true);
                $this->settings->setDays(intval($data["enabled"]["days"]));
                $this->settings->setUdfField(intval($data["enabled"]["udf_field"]));
            } else {
                $this->settings->setEnabled(false);
            }
        } else {
            $this->settings->setEnabled(boolval($data["enabled"]["value"]));
            $this->settings->setDays(intval(($this->settings->isEnabled() ? $data["enabled"]["group_values"]
                : $data["enabled"])["dependant_group"]["days"]));
            $this->settings->setUdfField(intval(($this->settings->isEnabled() ? $data["enabled"]["group_values"]
                : $data["enabled"])["dependant_group"]["udf_field"]));
        }

        self::srLearningProgressReset()->learningProgressReset()->storeSettings($this->settings);
    }
}
