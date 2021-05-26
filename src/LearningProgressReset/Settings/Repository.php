<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings;

use ilDBConstants;
use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Repository as LearningProgressResetRepository;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method\DisabledMethod;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method\Repository as MethodsRepository;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings
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
     * Repository constructor
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
     * @internal
     */
    public function dropTables()/* : void*/
    {
        self::dic()->database()->dropTable(Settings::TABLE_NAME, false);
        $this->methods()->dropTables();
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
    public function getEnabledSettings() : array
    {
        $result = self::dic()->database()->queryF('
SELECT ' . Settings::TABLE_NAME . '.obj_id
FROM object_data
INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id
INNER JOIN ' . Settings::TABLE_NAME . ' ON object_data.obj_id=' . Settings::TABLE_NAME . '.obj_id
WHERE enabled!=%s
AND object_reference.deleted IS NULL
AND ' . self::dic()->database()->in("type", LearningProgressResetRepository::OBJECT_TYPES, false, ilDBConstants::T_TEXT), [ilDBConstants::T_INTEGER], [DisabledMethod::ID]);

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
     * @internal
     */
    public function installTables()/* : void*/
    {
        Settings::updateDB();
        $this->methods()->installTables();
    }


    /**
     * @return MethodsRepository
     */
    public function methods() : MethodsRepository
    {
        return MethodsRepository::getInstance();
    }


    /**
     * @param Settings $settings
     */
    public function storeSettings(Settings $settings)/* : void*/
    {
        $settings->store();
    }
}
