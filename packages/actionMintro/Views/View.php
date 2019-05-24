<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMintro\Views;

use Bootstrap\Views\BootstrapView;


class View extends BootstrapView {

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMintro\Components\Components
     */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }


    public function tab1(){
        $this->layout = new \stdClass();

        $logo = $this->model->getConfigParam('actionimage1');
        $subject = $this->model->getConfigParam('subject');

        if($logo){
            $margin = round($this->screen_width / 8,0);
            $this->layout->scroll[] = $this->getComponentImage($logo,['priority' => '1'],['margin' => '140 '.$margin.' 30 '.$margin]);
        }

        $this->layout->scroll[] = $this->getComponentText($subject,['style' => 'intro_header']);

        $this->setSwipe();

        $this->layout->footer[] = $this->getComponentText('{#sign_up#}',[
            'onclick' => $this->getOnclickOpenAction('register'),
            'style' => 'intro_signup_button','uppercase' => true
        ]);

        $this->layout->footer[] = $this->getComponentText('{#already_a_user#}?',[
            'style' => 'intro_small_text'
        ]);

        $this->layout->footer[] = $this->getComponentText('{#login#}',[
            'onclick' => $this->getOnclickOpenAction('login'),
            'style' => 'intro_login_button','uppercase' => true
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(5);


        return $this->layout;
    }

    public function setSwipe(){
        $screens = $this->model->getConfigParam('screens');
        $screens = explode(';',$screens);

        foreach($screens as $screen){
            $col[] = $this->getComponentText($screen,['style' => 'intro_text']);
        }

        $this->layout->scroll[] = $this->getComponentSwipe($col,['hide_scrollbar' => 1]);
        $this->layout->scroll[] = $this->getComponentSwipeAreaNavigation('#ffffff','#4Dffffff');
    }


    public function getDivs(){
        $divs = new \stdClass();

        /* look for traits under the components */
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }



}
