<?php

namespace srag\DIC\SrLearningProgressReset\DIC;

use ILIAS\DI\Container;
use srag\DIC\SrLearningProgressReset\Database\DatabaseDetector;
use srag\DIC\SrLearningProgressReset\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\SrLearningProgressReset\DIC
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database() : DatabaseInterface
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
