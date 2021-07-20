<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\LearningProgressResetSettingsGUI;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class ilSrLearningProgressResetUIHookGUI
 */
class ilSrLearningProgressResetUIHookGUI extends ilUIHookPluginGUI
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const GET_PARAM_REF_ID = "ref_id";
    const GET_PARAM_TARGET = "target";
    const PAR_SUB_TABS = "sub_tabs";
    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;


    /**
     * @inheritDoc
     */
    public function modifyGUI(/*string*/ $a_comp, /*string*/ $a_part, /*array*/ $a_par = []) : void
    {
        if ($a_part === self::PAR_SUB_TABS) {

            if (self::dic()->ctrl()->getCmdClass() === strtolower(ilLPListOfObjectsGUI::class) || self::dic()->ctrl()->getCmdClass() === strtolower(ilLPListOfSettingsGUI::class)) {

                LearningProgressResetSettingsGUI::addTabs($this->getRefId());
            }
        }
    }


    /**
     * @return int|null
     */
    protected function getRefId() : ?int
    {
        $obj_ref_id = filter_input(INPUT_GET, self::GET_PARAM_REF_ID);

        if ($obj_ref_id === null) {
            $param_target = filter_input(INPUT_GET, self::GET_PARAM_TARGET);

            $obj_ref_id = explode("_", $param_target)[1];
        }

        $obj_ref_id = intval($obj_ref_id);

        if ($obj_ref_id > 0) {
            return $obj_ref_id;
        } else {
            return null;
        }
    }
}
