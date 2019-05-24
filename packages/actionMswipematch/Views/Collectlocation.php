<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;

class Collectlocation extends BootstrapView {

    /* @var \packages\actionMswipematch\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj) {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        
        if($this->model->getConfigParam('actionimage1')){
            $this->layout->scroll[] = $this->getComponentImage($this->model->getConfigParam('actionimage1'),
                [],
                ['margin' => '120 80 10 80']);
            $margin = 20;
        } else {
            $margin = 140;
        }

        $this->layout->scroll[] = $this->getComponentText('{#collect_location_matching#}', array(), array(
            'padding' => $margin.' 40 10 40',
            'text-align' => 'center',
            'font-size' => '27',
            'font-ios' => 'Lato-Light',
            'font-android' => 'Lato-Light',
        ));

        $onclick[] = $this->getOnclickLocation(['sync_open' => 1]);
        $onclick[] = $this->getOnclickOpenAction('people',false,['sync_open' => 1]);

        $this->layout->footer[] = $this->getComponentSpacer('20');
        $this->layout->footer[] = $this->uiKitButtonHollow('{#enable_location#}',[
            'onclick' => $onclick
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