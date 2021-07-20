<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Form;

use ilNonEditableValueGUI;
use ilSrLearningProgressResetPlugin;
use ilUserDefinedFields;
use srag\CustomInputGUIs\SrLearningProgressReset\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\SrLearningProgressReset\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\LearningProgressResetSettingsGUI;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method\AbstractMethod;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method\DisabledMethod;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method\ExternalDateMethod;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method\UdfFieldDateMethod;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Settings;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Form
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
            "method"                        => [
                "value"        => $this->settings->getMethod(),
                "group_values" => (function () : array {
                    switch ($this->settings->getMethod()) {
                        case UdfFieldDateMethod::ID:
                            return [
                                UdfFieldDateMethod::KEY => $this->settings->getUdfFieldDate()
                            ];
                        case ExternalDateMethod::ID:
                            return [
                                ExternalDateMethod::KEY . "_url" => $this->settings->getExternalDateUrl()
                            ];
                        case DisabledMethod::ID:
                        default:
                            return [];
                    }
                })()
            ],
            "days"                          => $this->settings->getDays(),
            "set_date_to_today_after_reset" => $this->settings->isSetDateToTodayAfterReset()
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $disabled_files = [
            "_" => new InputGUIWrapperUIInputComponent(new ilNonEditableValueGUI())
        ];
        $udf_field_fields = [
            UdfFieldDateMethod::KEY => self::dic()->ui()->factory()->input()->field()->select(self::plugin()
                ->translate(UdfFieldDateMethod::KEY, LearningProgressResetSettingsGUI::LANG_MODULE),
                [0 => ""] + array_map(function (array $field) : string {
                    return $field["field_name"];
                }, ilUserDefinedFields::_getInstance()->getDefinitions()),
                self::plugin()->translate(UdfFieldDateMethod::KEY . "_info", LearningProgressResetSettingsGUI::LANG_MODULE, [AbstractMethod::DATE_FORMAT]))->withRequired(true)
        ];
        $external_date_fields = [
            ExternalDateMethod::KEY . "_url" => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate(ExternalDateMethod::KEY
                . "_url", LearningProgressResetSettingsGUI::LANG_MODULE),
                (intval(DEVMODE) === 1 ? self::plugin()->translate(ExternalDateMethod::KEY . "_url_debug_info", LearningProgressResetSettingsGUI::LANG_MODULE)
                    : ""))->withRequired(true)
        ];
        $method = self::dic()->ui()->factory()->input()->field()->switchableGroup([
            DisabledMethod::ID     => self::dic()
                ->ui()
                ->factory()
                ->input()
                ->field()
                ->group($disabled_files, self::plugin()->translate(DisabledMethod::KEY, LearningProgressResetSettingsGUI::LANG_MODULE)),
            UdfFieldDateMethod::ID => self::dic()
                ->ui()
                ->factory()
                ->input()
                ->field()
                ->group($udf_field_fields, self::plugin()->translate(UdfFieldDateMethod::KEY, LearningProgressResetSettingsGUI::LANG_MODULE)),
            ExternalDateMethod::ID => self::dic()->ui()->factory()->input()->field()->group($external_date_fields,
                self::plugin()->translate(ExternalDateMethod::KEY, LearningProgressResetSettingsGUI::LANG_MODULE) . "<br>" . self::plugin()
                    ->translate(ExternalDateMethod::KEY . "_info", LearningProgressResetSettingsGUI::LANG_MODULE))->withByline(self::plugin()
                ->translate(ExternalDateMethod::KEY . "_info", LearningProgressResetSettingsGUI::LANG_MODULE))
            // TODO `withByline` not work in ILIAS 6 group (radio), so temporary in label
        ], self::plugin()->translate("method", LearningProgressResetSettingsGUI::LANG_MODULE))->withRequired(true);

        $fields = [
            "method"                        => $method,
            "days"                          => self::dic()->ui()->factory()->input()->field()->numeric(self::plugin()->translate("days", LearningProgressResetSettingsGUI::LANG_MODULE),
                self::plugin()->translate("days_info", LearningProgressResetSettingsGUI::LANG_MODULE))->withRequired(true),
            "set_date_to_today_after_reset" => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()
                ->translate("set_date_to_today_after_reset", LearningProgressResetSettingsGUI::LANG_MODULE),
                self::plugin()->translate("set_date_to_today_after_reset_info", LearningProgressResetSettingsGUI::LANG_MODULE))
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
        switch (strval($data["method"][0])) {
            case UdfFieldDateMethod::ID;
                $this->settings->setMethod(UdfFieldDateMethod::ID);
                $this->settings->setUdfFieldDate(strval($data["method"][1][UdfFieldDateMethod::KEY]));
                break;
            case ExternalDateMethod::ID;
                $this->settings->setMethod(ExternalDateMethod::ID);
                $this->settings->setExternalDateUrl(strval($data["method"][1][ExternalDateMethod::KEY . "_url"]));
                break;
            case DisabledMethod::ID:
            default:
                $this->settings->setMethod(DisabledMethod::ID);
                break;
        }
        $this->settings->setDays(intval($data["days"]));
        $this->settings->setSetDateToTodayAfterReset(boolval($data["set_date_to_today_after_reset"]));
        self::srLearningProgressReset()->learningProgressReset()->settings()->storeSettings($this->settings);
    }
}
