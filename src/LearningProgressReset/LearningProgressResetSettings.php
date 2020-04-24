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
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $udf_field = "";
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
    public function getObjId() : int
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId(int $obj_id) : void
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
    public function setDays(int $days) : void
    {
        $this->days = $days;
    }


    /**
     * @return string
     */
    public function getUdfField() : string
    {
        return $this->udf_field;
    }


    /**
     * @param string $udf_field
     */
    public function setUdfField(string $udf_field) : void
    {
        $this->udf_field = $udf_field;
    }
}
