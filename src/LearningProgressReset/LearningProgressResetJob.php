<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset;

use DateTime;
use ilCronJob;
use ilCronJobResult;
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
    const LANG_MODULE = "learning_progress_reset_job";
    /**
     * @var ilObjUser[]
     */
    protected $user_instance_cache = [];


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
        return ilSrLearningProgressResetPlugin::PLUGIN_NAME . ": " . self::plugin()->translate("title", self::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return self::plugin()->translate("description", self::LANG_MODULE);
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
    public function getDefaultScheduleValue()/* : ?int*/
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

        $all_settings = self::srLearningProgressReset()->learningProgressReset()->getAllSettings();

        $count_users = 0;
        $count_reset = 0;

        foreach ($all_settings as $settings) {
            foreach (
                array_unique(array_merge($settings->getObject()->getMembersObject()->getMembers(),
                    $settings->getObject()->getMembersObject()->getTutors(), $settings->getObject()->getMembersObject()->getAdmins())) as $user_id
            ) {
                $count_users++;

                $user = $this->getUserInstance($user_id);

                $udf_values = $user->getUserDefinedData();

                if (empty($settings->getUdfField())
                    || empty($udf_value_date = strval($udf_values["f_" . ($settings->getUdfField())]))
                    || empty($udf_value_date
                        = DateTime::createFromFormat(Settings::DATE_FORMAT, $udf_value_date))
                ) {
                    continue;
                }

                $udf_value_date->setTime(0, 0, 0, 0);

                if (($diff = intval(($time - $udf_value_date->getTimestamp()) / (60 * 60 * 24))) !== $settings->getDays()) {
                    continue;
                }

                ilLPStatus::writeStatus($settings->getObjId(), $user_id, ilLPStatus::LP_STATUS_NOT_ATTEMPTED);

                $count_reset++;
            }
        }

        $result->setStatus(ilCronJobResult::STATUS_OK);

        $result->setMessage(nl2br(self::plugin()->translate("result", self::LANG_MODULE, [
            count($all_settings),
            $count_users,
            $count_reset
        ]), false));

        return $result;
    }


    /**
     * @param int $user_id
     *
     * @return ilObjUser
     */
    protected function getUserInstance(int $user_id) : ilObjUser
    {
        if (!isset($this->user_instance_cache[$user_id])) {
            $this->user_instance_cache[$user_id] = new ilObjUser($user_id);
        }

        return $this->user_instance_cache[$user_id];
    }
}
