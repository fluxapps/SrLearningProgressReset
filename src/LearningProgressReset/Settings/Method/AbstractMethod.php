<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method;

use DateTime;
use Exception;
use ilLogLevel;
use ilLPStatus;
use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Settings;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;
use Throwable;

/**
 * Class AbstractMethod
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractMethod
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const DATE_FORMAT = "Y-m-d";
    /**
     * @var int
     *
     * @abstract
     */
    const ID = "";
    /**
     * @var string
     *
     * @abstract
     */
    const KEY = "";
    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;
    /**
     * @var int|null
     */
    private static $current_time_cache = null;
    /**
     * @var Settings
     */
    protected $settings;


    /**
     * AbstractMethod constructor
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }


    /**
     * @return int
     */
    protected static function getCurrentTime() : int
    {
        if (self::$current_time_cache === null) {
            self::$current_time_cache = time();
        }

        return self::$current_time_cache;
    }


    /**
     * @param int $count_members
     * @param int $count_learning_progress_reset
     * @param int $count_set_date_to_today
     * @param int $count_errors
     */
    public function resetLearningProgressOfMembers(int &$count_members, int &$count_learning_progress_reset, int &$count_set_date_to_today, int &$count_errors)/* : void*/
    {
        try {
            foreach (
                array_unique(array_merge($this->settings->getObject()->getMembersObject()->getMembers(), $this->settings->getObject()->getMembersObject()->getTutors(),
                    $this->settings->getObject()->getMembersObject()->getAdmins())) as $user_id
            ) {
                $count_members++;

                $this->resetLearningProgress($user_id, $count_learning_progress_reset, $count_set_date_to_today, $count_errors);
            }
        } catch (Throwable $ex) {
            self::dic()->logger()->root()->log($ex->__toString(), ilLogLevel::ERROR);
            $count_errors++;
        }
    }


    /**
     * @param int    $user_id
     * @param string $message
     *
     * @return Throwable
     */
    protected function exception(int $user_id, string $message) : Throwable
    {
        $fields = [
            "object_id" => $this->settings->getObjId(),
            "user_id"   => $user_id,
            "class"     => get_class($this),
            "message"   => $message
        ];

        return new Exception(implode(" | ", array_map(function (string $key, string $value) : string {
            return ($key . " : " . $value);
        }, array_keys($fields), $fields)));
    }


    /**
     * @param int $user_id
     *
     * @return string
     *
     * @throws Throwable
     */
    protected abstract function getDate(int $user_id) : string;


    /**
     * @param int $user_id
     * @param int $count_learning_progress_reset
     * @param int $count_set_date_to_today
     * @param int $count_errors
     */
    protected function resetLearningProgress(int $user_id, int &$count_learning_progress_reset, int &$count_set_date_to_today, int &$count_errors)/* : void*/
    {
        try {
            if (!$this->shouldResetLearningProgress($user_id)) {
                return;
            }

            $this->resetLearningProgressObject($user_id);

            $count_learning_progress_reset++;

            if ($this->settings->isSetDateToTodayAfterReset()) {
                $this->setDateToToday($user_id);

                $count_set_date_to_today++;
            }
        } catch (Throwable $ex) {
            self::dic()->logger()->root()->log($ex->__toString(), ilLogLevel::ERROR);
            $count_errors++;
        }
    }


    /**
     * @param int $user_id
     *
     * @throws Throwable
     */
    protected function resetLearningProgressObject(int $user_id)/* : void*/
    {
        ilLPStatus::writeStatus($this->settings->getObjId(), $user_id, ilLPStatus::LP_STATUS_NOT_ATTEMPTED);
    }


    /**
     * @param int $user_id
     *
     * @return bool
     *
     * @throws Throwable
     */
    protected abstract function setDateToToday(int $user_id) : bool;


    /**
     * @param int $user_id
     *
     * @return bool
     *
     * @throws Throwable
     */
    protected function shouldResetLearningProgress(int $user_id) : bool
    {
        $time = self::getCurrentTime();

        if (empty($date = $this->getDate($user_id))
            || empty($date = DateTime::createFromFormat(self::DATE_FORMAT, $date))
        ) {
            return false;
        }

        $date->setTime(0, 0, 0, 0);

        if (($diff = intval(($time - $date->getTimestamp()) / (60 * 60 * 24))) !== $this->settings->getDays()) {
            return false;
        }

        return true;
    }
}
