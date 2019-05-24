<?php

namespace packages\actionMswipematch\themes\matchswapp\Views;

use Bootstrap\Views\BootstrapView;

class Infinite extends BootstrapView {

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
        $logo = $this->getData('logoimage', 'string');
        $menu = $this->getData('menu', 'array');
        $icon_color = $this->getData('icon_color', 'string');
        $location = $this->getData('collect_location', 'bool');

        if($location){
            $this->layout->onload[] = $this->getOnclickLocation();
        }

        /* top bar if logo is set */
        if($logo){
            $params['mode'] = 'sidemenu';
            $params['logo'] = $logo;
            $params['hairline'] = '#e5e5e5';
            $params['icon_color'] = $icon_color;

            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }

            $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
        }

        $this->layout->overlay[] = $this->components->getListSwipeFloatingButton('list');
        $this->layout->overlay[] = $this->components->getCheckinFloatingButton();

        $this->layout->scroll[] = $this->components->uiKitInfiniteUserList($users,['instaclick_command' => 'Controller/recordinstaclick/']);

        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();
        return $divs;
    }

}