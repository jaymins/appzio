<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Unlock extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();
        $booking = $this->getData('booking_id', 'num');
        $this->layout->header[] = $this->components->getBigNexudusHeader('{#unlock_booth#}');

        if($booking){
            $this->layout->scroll[] = $this->getComponentSpacer(80);
            $this->layout->scroll[] = $this->getComponentFullPageLoaderAnimated(['color' => '#ffffff','text' => '{#openng_your_next_booking#}']);
            $this->layout->onload[] = $this->getOnclickOpenAction(
                'viewbooking',false,[
                    'id' => $booking,
                    'sync_open' => 1
                ]
            );

            return $this->layout;
        }





        $this->layout->scroll[] = $this->getComponentSpacer(20);
        $this->layout->scroll[] = $this->getComponentText('{#no_active_booking#}',[
            'style' => 'nexudus_uikit_formheader'
        ]);

        return $this->layout;
    }

    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }



}
