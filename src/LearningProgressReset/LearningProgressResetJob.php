<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset;

use ilCronJob;
use ilCronJobResult;
use ilCronManager;
use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class LearningProgressResetJob
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset
 */
class LearningProgressResetJob extends ilCronJob
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const CRON_JOB_ID = ilSrLearningProgressResetPlugin::PLUGIN_ID;
    const LANG_MODULE = "learning_progress_reset_job";
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
    public function getDescription() : string
    {
        return self::plugin()->translate("description", self::LANG_MODULE);
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
    public function run() : ilCronJobResult
    {
        $result = new ilCronJobResult();

        $methods = self::srLearningProgressReset()->learningProgressReset()->settings()->methods()->getEnabledMethods();

        $count_members = 0;
        $count_learning_progress_reset = 0;
        $count_set_date_to_today = 0;
        $count_errors = 0;

        foreach ($methods as $method) {
            $method->resetLearningProgressOfMembers($count_members, $count_learning_progress_reset, $count_set_date_to_today, $count_errors);

            ilCronManager::ping($this->getId());
        }

        $result->setStatus($count_errors > 0 ? ilCronJobResult::STATUS_FAIL : ilCronJobResult::STATUS_OK);

        $result->setMessage(nl2br(self::plugin()->translate("result", self::LANG_MODULE, [
            count($methods),
            $count_members,
            $count_learning_progress_reset,
            $count_set_date_to_today,
            $count_errors
        ]), false));

        return $result;
    }
}
