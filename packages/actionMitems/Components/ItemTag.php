<?php

namespace packages\actionMitems\Components;

use Bootstrap\Components\BootstrapComponent;

trait ItemTag
{

    /**
     * @param $text
     * @return \stdClass
     */
    public function getItemTag($text)
    {
        /** @var BootstrapComponent $this */
        return $this->getComponentText($text, array('style' => 'item_tag'));
    }
}