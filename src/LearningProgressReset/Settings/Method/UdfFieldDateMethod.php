<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method;

use ilObjUser;

/**
 * Class UdfFieldDateMethod
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UdfFieldDateMethod extends AbstractMethod
{

    const ID = 1;
    const KEY = "udf_field_date";
    /**
     * @var ilObjUser[]
     */
    private static $user_instances_cache = [];


    /**
     * @param int $user_id
     *
     * @return ilObjUser
     */
    private static function getUserInstance(int $user_id) : ilObjUser
    {
        if (!isset(self::$user_instances_cache[$user_id])) {
            self::$user_instances_cache[$user_id] = new ilObjUser($user_id);
        }

        return self::$user_instances_cache[$user_id];
    }


    /**
     * @inheritDoc
     */
    protected function getDate(int $user_id) : string
    {
        $user = self::getUserInstance($user_id);

        $udf_values = $user->getUserDefinedData();

        if (
            empty($this->settings->getUdfFieldDate())
            || empty($udf_value_date = strval($udf_values["f_" . $this->settings->getUdfFieldDate()]))
        ) {
            throw $this->exception($user_id, "Invalid udf field : " . $this->settings->getUdfFieldDate());
        }

        return $udf_value_date;
    }


    /**
     * @inheritDoc
     */
    protected function setDateToToday(int $user_id) : bool
    {
        $user = self::getUserInstance($user_id);

        if (empty($this->settings->getUdfFieldDate())) {
            return false;
        }

        $user->setUserDefinedData([
            $this->settings->getUdfFieldDate() => date(self::DATE_FORMAT, self::getCurrentTime())
        ]);

        $user->updateUserDefinedFields();

        return true;
    }
}
