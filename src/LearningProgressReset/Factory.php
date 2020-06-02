<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset;

use ilSrLearningProgressResetPlugin;
use srag\DIC\SrLearningProgressReset\DICTrait;
use srag\Plugins\SrLearningProgressReset\LearningProgressReset\Form\FormBuilder;
use srag\Plugins\SrLearningProgressReset\Utils\SrLearningProgressResetTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset
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
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return Settings
     */
    public function newInstance() : Settings
    {
        $settings = new Settings();

        return $settings;
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
     * @return LearningProgressResetJob
     */
    public function newJobInstance() : LearningProgressResetJob
    {
        $job = new LearningProgressResetJob();

        return $job;
    }
}
