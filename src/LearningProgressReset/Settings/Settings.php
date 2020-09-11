<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings;

use ActiveRecord;
use arConnector;
use ilObject;
use ilObjectFactory;
use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method\DisabledMethod;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class Settings
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Settings extends ActiveRecord
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;
    const TABLE_NAME = ilSrLearningProgressResetPlugin::PLUGIN_ID . "_obj_set";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $days = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_is_notnull   true
     */
    protected $enabled = DisabledMethod::ID;
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  true
     */
    protected $external_date_url = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected $obj_id;
    /**
     * @var ilObject|null
     */
    protected $object = null;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $set_date_to_today_after_reset = false;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $udf_field = 0;


    /**
     * Settings constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, /*?*/ arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @return int
     */
    public function getDays() : int
    {
        return $this->days;
    }


    /**
     * @param int $days
     */
    public function setDays(int $days)/* : void*/
    {
        $this->days = $days;
    }


    /**
     * @return string
     */
    public function getExternalDateUrl() : string
    {
        return $this->external_date_url;
    }


    /**
     * @param string $external_date_url
     */
    public function setExternalDateUrl(string $external_date_url)/* : void*/
    {
        $this->external_date_url = $external_date_url;
    }


    /**
     * @return int
     */
    public function getMethod() : int
    {
        return $this->enabled;
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId(int $obj_id)/* : void*/
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @return ilObject
     */
    public function getObject() : ilObject
    {
        if ($this->object === null) {
            $this->object = ilObjectFactory::getInstanceByObjId($this->obj_id, false);
        }

        return $this->object;
    }


    /**
     * @return int
     */
    public function getUdfFieldDate() : int
    {
        return $this->udf_field;
    }


    /**
     * @return bool
     */
    public function isSetDateToTodayAfterReset() : bool
    {
        return $this->set_date_to_today_after_reset;
    }


    /**
     * @param bool $set_date_to_today_after_reset
     */
    public function setSetDateToTodayAfterReset(bool $set_date_to_today_after_reset)/* : void*/
    {
        $this->set_date_to_today_after_reset = $set_date_to_today_after_reset;
    }


    /**
     * @param int $method
     */
    public function setMethod(int $method)/* : void*/
    {
        $this->enabled = $method;
    }


    /**
     * @param int $udf_field_date
     */
    public function setUdfFieldDate(int $udf_field_date)/* : void*/
    {
        $this->udf_field = $udf_field_date;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "set_date_to_today_after_reset":
                return ($field_value ? 1 : 0);

            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "set_date_to_today_after_reset":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
