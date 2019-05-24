<?php

namespace packages\actionMregister\themes\demoreg\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\demoreg\Components\Components;

class View extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->setHeader();

        /* get the data defined by the controller */
        $fieldlist = $this->getData('fieldlist','array');

        //$this->addCalendar();

        foreach($fieldlist as $field){
            $this->addField_page1($field);
        }

        $this->layout->footer[] = $this->uiKitDivider();
        $this->layout->footer[] = $this->components->getTerms(true);
        
        $this->layout->footer[] = $this->uiKitButtonFilled('{#sign_up#}',array(
            'onclick' => $this->getOnclickSubmit('signup')),
            array('margin' => '15 80 15 80')
        );

        return $this->layout;
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
            $this->layout->scroll[] = $this->getComponentSpacer($height);
        }


        if(isset($image_file)){
            $this->layout->scroll[] = $this->getComponentImage( $image_file );
        }

    }



}