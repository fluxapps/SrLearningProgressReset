<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method;

use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Settings;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use SrLearningProgressResetTrait;

    const METHODS
        = [
            DisabledMethod::ID     => DisabledMethod::class,
            ExternalDateMethod::ID => ExternalDateMethod::class,
            UdfFieldDateMethod::ID => UdfFieldDateMethod::class
        ];
    const PLUGIN_CLASS_NAME = ilSrLearningProgressResetPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
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
     * @param Settings $settings
     *
     * @return AbstractMethod
     */
    public function newInstance(Settings $settings) : AbstractMethod
    {
        $class = self::METHODS[$settings->getMethod()];

        $method = new $class($settings);

        return $method;
    }
}
