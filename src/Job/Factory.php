<?php

namespace srag\Plugins\SrLearningProgressReset\Job;

use ilCronJob;
use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\LearningProgressResetJob;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrLearningProgressReset\Job
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @param string $job_id
     *
     * @return ilCronJob|null
     */
    public function newInstanceById(string $job_id)/* : ?ilCronJob*/
    {
        switch ($job_id) {
            case LearningProgressResetJob::CRON_JOB_ID:
                return self::srLearningProgressReset()->learningProgressReset()->factory()->newJobInstance();

            default:
                return null;
        }
    }


    /**
     * @return ilCronJob[]
     */
    public function newInstances() : array
    {
        return [
            self::srLearningProgressReset()->learningProgressReset()->factory()->newJobInstance()
        ];
    }
}
