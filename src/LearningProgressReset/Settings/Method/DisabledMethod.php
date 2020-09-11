<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method;

/**
 * Class DisabledMethod
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DisabledMethod extends AbstractMethod
{

    const ID = 0;
    const KEY = "disabled";


    /**
     * @inheritDoc
     */
    protected function getDate(int $user_id) : string
    {
        throw $this->exception($user_id, self::class);
    }


    /**
     * @inheritDoc
     */
    protected function setDateToToday(int $user_id) : bool
    {
        throw $this->exception($user_id, self::class);
    }
}
