<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method;

use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Settings;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method
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
    public function dropTables() : void
    {

    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @return AbstractMethod[]
     */
    public function getEnabledMethods() : array
    {
        return array_map(function (Settings $settings) : AbstractMethod {
            return $this->factory()->newInstance($settings);
        }, self::srLearningProgressReset()->learningProgressReset()->settings()->getEnabledSettings());
    }


    /**
     * @internal
     */
    public function installTables() : void
    {

    }
}
