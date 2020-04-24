<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset;

use ilDBConstants;
use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


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
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @internal
     */
    public function dropTables() : void
    {
        self::dic()->database()->dropTable(LearningProgressResetSettings::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @return LearningProgressResetSettings[]
     */
    public function getAllLearningProgressResetSettings() : array
    {
        $result = self::dic()->database()->queryF('
SELECT ' . LearningProgressResetSettings::TABLE_NAME . '.obj_id
FROM object_data
INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id
INNER JOIN ' . LearningProgressResetSettings::TABLE_NAME . ' ON object_data.obj_id=' . LearningProgressResetSettings::TABLE_NAME . '.obj_id
WHERE type=%s
AND object_reference.deleted IS NULL', [ilDBConstants::T_TEXT], ["crs"]);

        $obj_ids = array_map(function (array $object) : int {
            return $object["obj_id"];
        }, self::dic()->database()->fetchAll($result));

        if (empty($obj_ids)) {
            return [];
        }

        return LearningProgressResetSettings::where("obj_id", $obj_ids)->get();
    }


    /**
     * @param int $obj_ref_id
     *
     * @return LearningProgressResetSettings
     */
    public function getLearningProgressResetSettings(int $obj_ref_id) : LearningProgressResetSettings
    {
        /**
         * @var LearningProgressResetSettings|null $learning_progress_reset_settings
         */

        $obj_id = self::dic()->objDataCache()->lookupObjId($obj_ref_id);

        $learning_progress_reset_settings = LearningProgressResetSettings::where(["obj_id" => $obj_id])->first();

        if ($learning_progress_reset_settings === null) {
            $learning_progress_reset_settings = $this->factory()->newInstance();

            $learning_progress_reset_settings->setObjId($obj_id);
        }

        return $learning_progress_reset_settings;
    }


    /**
     * @param int $user_id
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasAccess(int $user_id, int $obj_ref_id) : bool
    {
        return self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id);
    }


    /**
     * @internal
     */
    public function installTables() : void
    {
        LearningProgressResetSettings::updateDB();
    }


    /**
     * @param LearningProgressResetSettings $learning_progress_reset_settings
     */
    public function storeLearningProgressResetSettings(LearningProgressResetSettings $learning_progress_reset_settings) : void
    {
        $learning_progress_reset_settings->store();
    }
}
