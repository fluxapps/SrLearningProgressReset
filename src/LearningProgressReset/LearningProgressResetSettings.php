<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset;

use ActiveRecord;
use arConnector;
use ilObject;
use ilObjectFactory;
use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class LearningProgressResetSettings
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LearningProgressResetSettings extends ActiveRecord
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const TABLE_NAME = ilSrLearningProgressResetPlugin::PLUGIN_ID . "_obj_set";
    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;
    const DATE_FORMAT = "Y-m-d";
    const OBJECT_TYPES = ["crs"];


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
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
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $enabled = false;
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
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $udf_field = 0;
    /**
     * @var ilObject|null
     */
    protected $object = null;


    /**
     * LearningProgressResetSettings constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "enabled":
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
            case "enabled":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
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
     * @return bool
     */
    public function isEnabled() : bool
    {
        return $this->enabled;
    }


    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled)/* : void*/
    {
        $this->enabled = $enabled;
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
     * @return int
     */
    public function getUdfField() : int
    {
        return $this->udf_field;
    }


    /**
     * @param int $udf_field
     */
    public function setUdfField(int $udf_field)/* : void*/
    {
        $this->udf_field = $udf_field;
    }
}
