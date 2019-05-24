<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Home extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();

        $booking = $this->getData('booking','array');

        $this->layout->header[] = $this->components->getBigNexudusHeader('{#home#}',[
            'onclick' => $this->getOnclickOpenSidemenu(),
                'icon' => 'white_hamburger_icon.png'
            ]
            );

        if($booking){
            $this->layout->scroll[] = $this->components->getNexudusNextBookingBubble($booking);
        }

        $this->layout->scroll[] = $this->getComponentSpacer(10);

        $this->setMenuRow1();
        $this->setMenuRow2();
        $this->setMenuRow3();

        if($this->model->getSavedVariable('phone_verified') == 2){
            $this->layout->footer[] = $this->getComponentText('{#your_phone_number_is_not_verified#}. {#click_here_to_verify_it#}.',[
                'onclick' => $this->getOnclickSubmit('home/activateverify/')
            ],[
                'color' => '#ffffff','padding' => '10 10 10 10',
                'border-radius' => '8', 'background-color' => '#B3000000',
                'text-align' => 'center','font-size' => '15',
                'margin' => '10 40 10 40'
            ]);
        }


        $this->layout->footer[] = $this->getComponentSpacer(15);

        return $this->layout;
    }

    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }

    public function setMenuRow1(){
        $col[] = $this->components->getNexudusMainMenuButton([
            'onclick' => $this->getOnclickOpenAction('makebooking'),
            'icon' => 'icon-nexudus-plus.png',
            'title' => '{#make_a_booking#}'
        ]);

        $col[] = $this->components->getNexudusMainMenuButton([
            'onclick' => $this->getOnclickOpenAction('listbookings'),
            'icon' => 'icon-nexudus-booking.png',
            'title' => '{#my_bookings#}'
        ]);

        $col[] = $this->components->getNexudusMainMenuButton([
            'onclick' => $this->getOnclickOpenAction('pricing'),
            'icon' => 'icon-nexudus-pound.png',
            'title' => '{#pricing#}'
        ]);

        /*        $col[] = $this->components->getNexudusMainMenuButton([
                    'onclick' => $this->getOnclickOpenAction('locations'),
                    'icon' => 'icon-nexudus-location.png',
                    'title' => '{#locations#}'
                ]);*/

        $this->layout->scroll[] = $this->getComponentRow($col);
    }

    public function setMenuRow2(){

        $col[] = $this->components->getNexudusMainMenuButton([
            'onclick' => $this->getOnclickOpenAction('myaccount'),
            'icon' => 'icon-nexudus-person.png',
            'title' => '{#my_account#}'
        ]);

        $col[] = $this->components->getNexudusMainMenuButton([
            'onclick' => $this->getOnclickOpenAction('help'),
            'icon' => 'icon-nexudus-questionmark.png',
            'title' => '{#how_it_works#}'
        ]);

        $col[] = $this->components->getNexudusMainMenuButton([
            'onclick' => $this->getOnclickOpenAction('rules'),
            'icon' => 'icon-nexudus-rules.png',
            'title' => '{#rules#}'
        ]);

        $this->layout->scroll[] = $this->getComponentRow($col);
    }

    public function setMenuRow3(){

        $col[] = $this->components->getNexudusMainMenuButton([
            'onclick' => $this->getOnclickOpenAction('faq'),
            'icon' => 'icon-nexudus-help.png',
            'title' => '{#help#}'
        ]);

        $col[] = $this->components->getNexudusMainMenuButton([
            'onclick' => $this->getOnclickOpenAction('contact'),
            'icon' => 'icon-nexudus-email.png',
            'title' => '{#contact_us#}'
        ]);

        $this->layout->scroll[] = $this->getComponentRow($col);
    }



}
