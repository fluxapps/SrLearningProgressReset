<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset;

use ilCronJob;
use ilCronJobResult;
use ilDBConstants;
use ilLPStatus;
use ilObjUser;
use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class LearningProgressResetJob
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LearningProgressResetJob extends ilCronJob
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const CRON_JOB_ID = ilSrLearningProgressResetPlugin::PLUGIN_ID;
    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;


    /**
     * LearningProgressResetJob constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getId() : string
    {
        return self::CRON_JOB_ID;
    }


    /**
     * @inheritDoc
     */
    public function getTitle() : string
    {
        return ilSrLearningProgressResetPlugin::PLUGIN_NAME;
    }


    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return "";
    }


    /**
     * @inheritDoc
     */
    public function hasAutoActivation() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function hasFlexibleSchedule() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function getDefaultScheduleType() : int
    {
        return self::SCHEDULE_TYPE_DAILY;
    }


    /**
     * @inheritDoc
     */
    public function getDefaultScheduleValue() : ?int
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    public function run() : ilCronJobResult
    {
        $time = time();

        $result = new ilCronJobResult();

        $all_learning_progress_reset_settings = self::srLearningProgressReset()->learningProgressReset()->getAllLearningProgressResetSettings();

        $count_users = 0;
        $count_reset = 0;

        foreach ($all_learning_progress_reset_settings as $learning_progress_reset_settings) {
            foreach ($learning_progress_reset_settings->getObject()->getMembersObject()->getMembers() as $user_id) {
                $count_users++;

                $user = new ilObjUser($user_id);

                $udf_values = $user->getUserDefinedData();

                $field_id = $this->getUserDefinedFieldID($learning_progress_reset_settings->getUdfField());
                if (empty($field_id) || empty($udf_value_date = strtotime(strval($udf_values[($field_id = "f_" . $field_id)])))) {
                    continue;
                }

                if (intval(($time - $udf_value_date) / (60 * 60 * 24)) !== $learning_progress_reset_settings->getDays()) {
                    continue;
                }

                ilLPStatus::writeStatus($learning_progress_reset_settings->getObjId(), $user_id, ilLPStatus::LP_STATUS_NOT_ATTEMPTED);

                $count_reset++;
            }
        }

        $result->setStatus(ilCronJobResult::STATUS_OK);

        $result->setMessage(nl2br(self::plugin()->translate("job_result", LearningProgressResetSettingsGUI::LANG_MODULE, [
            count($all_learning_progress_reset_settings),
            $count_users,
            $count_reset
        ]), false));

        return $result;
    }


    /**
     * @param string $field_name
     *
     * @return int|null
     */
    protected function getUserDefinedFieldID(string $field_name) : ?int
    {
        $result = self::dic()->database()->queryF('SELECT field_id FROM udf_definition WHERE field_name=%s', [ilDBConstants::T_TEXT], [$field_name]);

        if (($row = $result->fetchAssoc()) !== false) {
            return intval($row["field_id"]);
        } else {
            return null;
        }
    }
}
