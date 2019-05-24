<?php

namespace packages\actionMswipematch\themes\matchswapp\Views;

use Bootstrap\Views\BootstrapView;

class Matching extends BootstrapView {

    /* @var \packages\actionMswipematch\themes\matchswapp\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj) {
        parent::__construct($obj);
    }


    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $users = $this->getData('users', 'mixed');
        $chat = $this->getData('chats', 'int');

        $logo = $this->getData('logoimage', 'string');
        $menu = $this->getData('menu', 'array');
        $bottom_menu = $this->getData('bottom_menu', 'num');

        /* top bar if logo is set */
        if($logo){
            $params['mode'] = 'sidemenu';
            $params['logo'] = $logo;

            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }

            $this->layout->overlay[] = $this->components->uiKitFauxTopBarTransparent($params);
        }

        $this->layout->scroll[] = $this->uiKitUserSwiperFullScreen($users, array('chat' => $chat,'bottom_menu' => $bottom_menu));

        if(!$this->model->getConfigParam('hide_bottom_navi')){
            $this->layout->overlay[] = $this->components->getListSwipeFloatingButton('swipe');
            $this->layout->overlay[] = $this->components->getCheckinFloatingButton();
        }


        return $this->layout;
    }



    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();

        return $divs;
    }
}