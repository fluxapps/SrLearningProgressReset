<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings;

use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Form\FormBuilder;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use SrLearningProgressResetTrait;

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
     * @param LearningProgressResetSettingsGUI $parent
     * @param Settings                         $settings
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(LearningProgressResetSettingsGUI $parent, Settings $settings) : FormBuilder
    {
        $form = new FormBuilder($parent, $settings);

        return $form;
    }


    /**
     * @return Settings
     */
    public function newInstance() : Settings
    {
        $settings = new Settings();

        return $settings;
    }
}
