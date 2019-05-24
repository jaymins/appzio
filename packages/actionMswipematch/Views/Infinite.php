<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;

class Infinite extends BootstrapView {

    /* @var \packages\actionMswipematch\Components\Components */
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
        $notification_count = $this->getData('notification_count', 'num');
        $checked_in = $this->getData('checked_in', 'bool');
        $original_dimension = $this->getData('original_image_dimensions', 'mixed');

        if($location){
            $this->layout->onload[] = $this->getOnclickLocation();
        }

        if($checked_in){
            $this->layout->header[] = $this->getComponentText('{#checked_in#} '.$checked_in);
        }

        /* top bar if logo is set */
        if($logo){
            $params['mode'] = 'sidemenu';
            $params['logo'] = $logo;
            $params['hairline'] = '#e5e5e5';
            $params['icon_color'] = $icon_color;
            $params['notification_count'] = $notification_count;

            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }

            $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
        }

        $this->layout->scroll[] = $this->components->uiKitInfiniteUserList($users,[
            'instaclick_command' => 'Controller/recordinstaclick/',
            'original_image_dimensions' => $original_dimension
        ]);
        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();
        return $divs;
    }

}