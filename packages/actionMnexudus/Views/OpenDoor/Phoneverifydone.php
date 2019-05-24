<?php

namespace packages\actionMnexudus\Views\OpenDoor;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Phoneverifydone extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();

        /* this will get called only once */
        $verify = $this->getData('send_verification', 'bool');

        $this->layout->header[] = $this->components->getBigNexudusHeader('{#verification_complete#}',[
                'onclick' => $this->getOnclickOpenSidemenu(),
                'icon' => 'white_hamburger_icon.png'
            ]
        );

        $col[] = $this->getComponentSpacer(40);
        $col[] = $this->getComponentText('{#your_phone_number_is_now_verified#}',
            ['style' => 'nexudus_uikit_formheader']);
        $col[] = $this->getComponentSpacer(10);
        $col[] = $this->getComponentText('{#and_you_can_open_door_to_booth_you_book#}',
            ['style' => 'nexudus_uikit_formheader']);
        $col[] = $this->getComponentSpacer(10);
        $col[] = $this->getComponentText('{#make_sure_to_keep_bluetooth_enabled_on_your_phone#}',
            ['style' => 'nexudus_uikit_formheader']);
        $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '0 20 0 20']);
        $this->layout->footer[] = $this->getComponentSpacer(15);
        $this->layout->footer[] = $this->uiKitButtonFilled('{#go_home#}',[
            'onclick' => $this->getOnclickSubmit('home/default/')
        ],[
            'background-color' => $this->color_top_bar_color,
            'color' => $this->color_top_bar_text_color,
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(30);

        return $this->layout;
    }

    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }



}
