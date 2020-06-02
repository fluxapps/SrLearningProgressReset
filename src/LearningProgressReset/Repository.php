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
    public function dropTables()/* : void*/
    {
        self::dic()->database()->dropTable(Settings::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @return Settings[]
     */
    public function getAllSettings() : array
    {
        $result = self::dic()->database()->queryF('
SELECT ' . Settings::TABLE_NAME . '.obj_id
FROM object_data
INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id
INNER JOIN ' . Settings::TABLE_NAME . ' ON object_data.obj_id=' . Settings::TABLE_NAME . '.obj_id
WHERE enabled=%s
AND object_reference.deleted IS NULL
AND ' . self::dic()->database()->in("type", Settings::OBJECT_TYPES, false, ilDBConstants::T_TEXT), [ilDBConstants::T_INTEGER], [true]);

        $obj_ids = array_map(function (array $object) : int {
            return $object["obj_id"];
        }, self::dic()->database()->fetchAll($result));

        if (empty($obj_ids)) {
            return [];
        }

        return Settings::where(["obj_id" => $obj_ids])->get();
    }


    /**
     * @param int $obj_ref_id
     *
     * @return Settings
     */
    public function getSettings(int $obj_ref_id) : Settings
    {
        /**
         * @var Settings|null $settings
         */

        $obj_id = self::dic()->objDataCache()->lookupObjId($obj_ref_id);

        $settings = Settings::where(["obj_id" => $obj_id])->first();

        if ($settings === null) {
            $settings = $this->factory()->newInstance();

            $settings->setObjId($obj_id);
        }

        return $settings;
    }


    /**
     * @param int $user_id
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasAccess(int $user_id, int $obj_ref_id) : bool
    {
        return (in_array(self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId($obj_ref_id)), Settings::OBJECT_TYPES)
            && self::dic()
                ->access()
                ->checkAccessOfUser($user_id, "write", "", $obj_ref_id));
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {
        Settings::updateDB();
    }


    /**
     * @param Settings $settings
     */
    public function storeSettings(Settings $settings)/* : void*/
    {
        $settings->store();
    }
}
