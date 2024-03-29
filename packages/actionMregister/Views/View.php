<?php

namespace packages\actionMregister\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMregister\Controllers\Components;
use stdClass;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView {

    /* @var \packages\actionMregister\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $this->setTopShadow();
        $this->setHeader();

        /* get the data defined by the controller */
        $fieldlist = $this->getData('fieldlist','array');

        //$this->addCalendar();

        foreach($fieldlist as $field){
            $this->addField_page1($field);
        }

        /* route: Contoller/action. Controller defines the view file.
            this is the more complex routing example just an example. See
            Pagetwo.php for more straight-forward way of doing it. This
            can pass any number of parameters with the click.
        */

        $btn[] = $this->getComponentText('Sign Up',
            array('style' => 'mreg_btn',
                'onclick' => $this->getOnclickRoute(
                'Controller/default/signup',
              true,
                array('mytestid' => 'Flower sent from Default view','exampleid' => 393393),
                true
            )));

        $this->layout->footer[] = $this->getComponentRow($btn,array('style' => 'mreg_btn_row'));
        return $this->layout;
    }

    public function addCalendar(){
        $date = $this->model->getSubmittedVariableByName('birthdate') ? $this->model->getSubmittedVariableByName('birthdate') : time();

        $params['variable'] = 'birthdate';
        $params['date'] = $date;
        $params['style'] = new stdClass();
        $params['selection_style'] = new stdClass();

        $params['selection_style']->color = '#ffffff';
        $name = 'background-color';
        $params['style']->$name = '#ffffff';

        $params['style']->color = '#FF00FF';
        $params['style']->height = 200;
        $params['style']->margin = '20 100 0 100';
        $params['style']->padding = '10 10 10 10';
        $name = 'background-color';
        $params['style']->$name = '#ffffff';

        $this->layout->scroll[] = $this->getComponentCalendar($params);
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */

/*    public function getDivs(){
        $divs = new \stdClass();

        $divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }*/

    public function addField_page1($field){
        switch($field){

            case 'mreg_collect_photo':
                $this->layout->scroll[] = $this->components->getPhotoField('mreg_collect_photo');
                break;

            case 'mreg_collect_full_name':
                $content[] = $this->components->getIconField('firstname','{#first_name#}','mreg-icon-person.png');
                $content[] = $this->getDivider();
                $content[] = $this->components->getIconField('lastname','{#last_name#}','mreg-icon-person.png');
                $this->layout->scroll[] = $this->components->getShadowBox($this->getComponentColumn($content,array(),array(
                    'width' => '100%','background-color' => '#87cefa','color' => '#000'
                )));
                break;

            case 'mreg_collect_phone':
                $content[] = $this->components->getPhoneNumberField($this->getData('current_country','string'),'phone','{#phone#}','mreg-icon-phone.png');
                $this->layout->scroll[] = $this->components->getShadowBox($this->getComponentColumn($content,array(),array(
                    'width' => '100%'
                )));
                break;


            case 'mreg_collect_email':
                $content[] = $this->components->getIconField('email','{#email#}','mreg-icon-mail.png');
                $content[] = $this->getDivider();
                $content[] = $this->components->getIconField('password','{#password#}','mreg-icon-key.png');
                $content[] = $this->getDivider();
                $content[] = $this->components->getIconField('password_again','{#password_again#}','mreg-icon-key.png');
                $this->layout->scroll[] = $this->components->getShadowBox($this->getComponentColumn($content,array(),array(
                    'width' => '100%','background-color' => '#87cefa'
                )));

                break;
        }
    }

    public function getDivider(){
        return $this->getComponentText('',array('style' => 'mreg_divider'));
    }

    private function setTopShadow(){
        $txt[] = $this->getComponentText('');
        $this->layout->header[] = $this->getComponentRow($txt, array(), array(
            'background-color' => $this->color_top_bar_color,
            'parent_style' => 'mreg_top_shadow'
        ));
    }

    public function setHeader($padding=false){
        if($padding){
            $height = $padding;
        }elseif($this->aspect_ratio > 0.57) {
            $height = 10;
        } else {
            $height = 20;
        }

        if ( $this->model->getConfigParam( 'actionimage1' ) ) {
            $image_file = $this->model->getConfigParam( 'actionimage1' );
        } elseif ( $this->getImageFileName('login-logo.png') ) {
            $image_file = 'login-logo.png';
        }

        $this->layout->scroll[] = $this->getComponentSpacer($height);

        if(isset($image_file)){
            $this->layout->scroll[] = $this->getComponentImage( $image_file );
        }

        $this->layout->scroll[] = $this->getComponentSpacer('10');
    }


}