<?php


namespace packages\actionMplug\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMplug\Components\Components as Components;

class Showapp extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentImage('appzio-logo-positive.png',array('style' => 'mplug_logo'));

        $this->layout->scroll[] = $this->getComponentText('Your login was succesfull. Click to continue to app.',
            array('style' => 'mplug_introtext'));

        $this->layout->scroll[] = $this->getComponentSpacer('30');

        $this->layout->scroll[] = $this->getComponentText('By continuing, you agree to the Terms & Conditions. 
        Click to view the Terms & Conditions.',
            array('onclick' => $this->getOnclickOpenAction('terms',false,array(
                'open_popup' => 1,
            )),'style' => 'mplug_introtext')
        );

        $server = $this->getData('server', 'string');
        $api_key = $this->getData('api_key', 'string');

        $this->layout->scroll[] = $this->uiKitButtonFilled('{#continue#}',
            array('onclick' => $this->getOnclickOpenApp($server,$api_key))
            );

        //$this->layout->footer[] = $this->getComponentRow($btn,array('style' => 'mplug_btn_row'));
        return $this->layout;
    }



}