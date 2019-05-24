<?php

namespace packages\actionMitems\themes\fanshop\Components;

use Bootstrap\Components\BootstrapComponent;

trait AddButton
{
    public $isLiked;

    public function getAddButton($params = array())
    {

        $onclick = new \stdClass();
        $onclick->id = 'new';
        $onclick->action = 'open-action';
        $onclick->action_config = $this->model->getActionidByPermaname('createshopitem');
        $onclick->sync_close = 1;
        $onclick->sync_open = 1;

        $btn[] = $this->getComponentImage('fanshop_add_icon.png', array(
            'style' => 'fanshop_fanshop_add_item_button',
            'onclick' => $onclick,
            'bottom' => '10',
            'text-align' => 'center'
        ));

        $layout = new \stdClass();
        $layout->bottom = '10';
        $layout->height = '80';
        $layout->center = 0;
        $layout->width = '80';

        return $this->getComponentRow($btn,array('layout' => $layout),array('text-align' => 'center'));

    }
}