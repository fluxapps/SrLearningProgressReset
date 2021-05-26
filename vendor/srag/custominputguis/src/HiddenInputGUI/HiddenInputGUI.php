<?php

namespace srag\CustomInputGUIs\SrLearningProgressReset\HiddenInputGUI;

use ilHiddenInputGUI;
use srag\CustomInputGUIs\SrLearningProgressReset\Template\Template;
use srag\DIC\SrLearningProgressReset\DICTrait;

/**
 * Class HiddenInputGUI
 *
 * @package srag\CustomInputGUIs\SrLearningProgressReset\HiddenInputGUI
 */
class HiddenInputGUI extends ilHiddenInputGUI
{

    use DICTrait;

    /**
     * HiddenInputGUI constructor
     *
     * @param string $a_postvar
     */
    public function __construct(string $a_postvar = "")
    {
        parent::__construct($a_postvar);
    }


    /**
     * @return string
     */
    public function render() : string
    {
        $tpl = new Template("Services/Form/templates/default/tpl.property_form.html", true, true);

        $this->insert($tpl);

        return self::output()->getHTML($tpl);
    }
}
