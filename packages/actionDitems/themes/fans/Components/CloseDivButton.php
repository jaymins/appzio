<?php

namespace packages\actionDitems\themes\fans\Components;

use Bootstrap\Components\BootstrapComponent;

trait CloseDivButton
{
    public function getCloseDivButton($divId = '', $params = array())
    {
        /** @var BootstrapComponent $this */
        $title = isset($params['title']) ? $params['title'] : '{#go_back#}';

        return $this->getComponentRow(array(
            $this->uiKitIconButton($title, array(
                'onclick' => $this->getOnclickHideDiv($divId)
            ), array(
                'width' => '75%',
                'padding' => '15 0 15 0',
                'border-radius' => '25',
                'color' => '#323232',
                'border-color' => '#323232'
            ))
        ), array(), array(
            'text-align' => 'center'
        ));
    }
}