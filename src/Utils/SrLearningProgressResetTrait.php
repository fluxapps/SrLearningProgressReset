<?php

namespace srag\Plugins\SrLearningProgressReset\Utils;

use srag\Plugins\SrLearningProgressReset\Repository;

/**
 * Trait SrLearningProgressResetTrait
 *
 * @package srag\Plugins\SrLearningProgressReset\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait SrLearningProgressResetTrait
{

    /**
     * @return Repository
     */
    protected static function srLearningProgressReset() : Repository
    {
        return Repository::getInstance();
    }
}
