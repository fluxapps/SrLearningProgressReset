<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset;

use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Repository as SettingsRepository;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset
 */
final class Repository
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const OBJECT_TYPES = ["crs"];
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
        $this->settings()->dropTables();
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $user_id
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasAccess(int $user_id, int $obj_ref_id) : bool
    {
        return (in_array(self::dic()->objDataCache()->lookupType(self::dic()->objDataCache()->lookupObjId($obj_ref_id)), self::OBJECT_TYPES)
            && self::dic()->access()->checkAccessOfUser($user_id, "write", "", $obj_ref_id));
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {
        $this->settings()->installTables();
    }


    /**
     * @return SettingsRepository
     */
    public function settings() : SettingsRepository
    {
        return SettingsRepository::getInstance();
    }
}
