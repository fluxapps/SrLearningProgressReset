<?php

namespace srag\DIC\SrLearningProgressReset\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\SrLearningProgressReset\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
