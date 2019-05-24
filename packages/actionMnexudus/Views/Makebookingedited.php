<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Makebookingedited extends Makebooking2 {

    /* @var Components */
    public $components;
    public $theme;
    public $booking_data;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();
        $this->booking_data = $this->getData('booking_data', 'array');

        $this->setHeader();

        $col[] = $this->getComponentImage('nexudus-big-smiley.png',[],['width' => '150']);
        $this->layout->scroll[] = $this->getComponentColumn($col,[],['text-align' => 'center','margin' => '40 0 20 0']);
        $this->layout->scroll[] = $this->getComponentText('{#booking_updated#}',['style' => 'uikit_information_tile_title']);
        $this->layout->scroll[] = $this->getComponentText('{#your_information_has_been_updated#}',['style' => 'nexudus_uikit_formheader']);

        $onclick[] = $this->getOnclickSubmit('Makebooking/reset/1');
        $onclick[] = $this->getOnclickOpenAction('home');

        $this->layout->footer[] = $this->uiKitButtonFilled('{#go_home#}',[
            'onclick' => $onclick
        ],[
            'background-color' => $this->color_top_bar_color,
            'color' => $this->color_top_bar_text_color,
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(15);
        return $this->layout;
    }

    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }



}
