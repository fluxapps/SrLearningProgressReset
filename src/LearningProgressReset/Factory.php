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
     * @return LearningProgressResetSettings
     */
    public function newInstance() : LearningProgressResetSettings
    {
        $learning_progress_reset_settings = new LearningProgressResetSettings();

        return $learning_progress_reset_settings;
    }


    /**
     * @param LearningProgressResetSettingsGUI $parent
     * @param LearningProgressResetSettings    $learning_progress_reset_settings
     *
     * @return FormBuilder
     */
    public function newFormBuilderInstance(LearningProgressResetSettingsGUI $parent, LearningProgressResetSettings $learning_progress_reset_settings) : FormBuilder
    {
        $form = new FormBuilder($parent, $learning_progress_reset_settings);

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
