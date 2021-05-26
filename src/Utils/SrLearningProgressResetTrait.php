<?php

namespace srag\Plugins\SrLearningProgressReset\Utils;

use srag\Plugins\SrLearningProgressReset\Repository;

/**
 * Trait SrLearningProgressResetTrait
 *
 * @package srag\Plugins\SrLearningProgressReset\Utils
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
