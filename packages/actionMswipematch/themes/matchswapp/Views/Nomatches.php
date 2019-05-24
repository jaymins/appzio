<?php

namespace packages\actionMswipematch\themes\matchswapp\Views;

use Bootstrap\Views\BootstrapView;

class Nomatches extends BootstrapView {

    /* @var \packages\actionMswipematch\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj) {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();

        $menu = $this->getData('menu', 'array');

        /* top bar if logo is set */
        /* top bar if logo is set */
        $logo = $this->getData('logoimage', 'string');

        $params['mode'] = 'sidemenu';
        $params['color'] = $this->getData('icon_color', 'string');

        if($logo) {
            $params['logo'] = $logo;
        }

        $params['hairline'] = '#e5e5e5';
        $params['icon_color'] = 'white';

        if(!empty($menu)){
            $params['right_menu'] = $menu;
        }

        $this->layout->header[] = $this->components->uiKitFauxTopBar($params);


        $location = $this->getData('collect_location', 'bool');

        if($location){
            $this->layout->onload[] = $this->getOnclickLocation();
        }


        if($this->model->getConfigParam('actionimage1')){
            $col[] = $this->getComponentImage($this->model->getConfigParam('actionimage1'),[],['width' => '100','text-align' => 'center','margin' => '110 80 10 80','opacity' => '0.8']);
        } else {
            $col[] = $this->getComponentImage('no-matches-icon.png',[],['width' => '100','text-align' => 'center','margin' => '110 80 10 80','opacity' => '0.8']);
        }

        $col[] = $this->getComponentText('{#no_matches#}', array(), array(
            'padding' => '20 60 10 60',
            'text-align' => 'center',
            'color' => '#292929',
            'font-size' => '24',
            'font-ios' => 'Lato-Light',
            'font-android' => 'Lato-Light',
        ));

        $this->layout->scroll[] = $this->getComponentColumn($col,[],['text-align' => 'center']);

        $this->layout->footer[] = $this->getComponentSpacer('20');
        $this->layout->footer[] = $this->uiKitButtonHollow('{#search_again#}',[
            'onclick' => $this->getOnclickSubmit('default',['sync_open' => 1])
        ]);
        $this->layout->footer[] = $this->getComponentSpacer('20');

        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();

        return $divs;
    }

}