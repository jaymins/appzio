<?php


namespace packages\actionMplug\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMplug\Components\Components as Components;

class View extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();

        //$this->layout->header[] = $this->uiKitThreeColumnImageSwiper('Appzio Plug');

        $this->layout->scroll[] = $this->getComponentImage('appzio-logo-positive.png',array('style' => 'mplug_logo'));

        $this->layout->scroll[] = $this->getComponentText('This application is for previewing applications developed using Appzio platform. In order to proceed, please provide your server & the special username and password used to preview the application. Please refer to Appzio documentation on where to find this information.',
            array('style' => 'mplug_introtext'));

        if(isset($this->model->validation_errors) AND $this->model->validation_errors){
            foreach($this->model->validation_errors as $error){
                $this->layout->scroll[] = $this->getComponentText($error,array('style' => 'mplug_error'));
            }
        }

/*      $content[] = $this->components->getIconFieldUrl('server', '{#server#}','icons8-root_server.png');
        $content[] = $this->components->getComponentDivider();*/
        $content[] = $this->components->getIconField('username', '{#username#}','icons8-user_male.png');
        $content[] = $this->components->getComponentDivider();
        $content[] = $this->components->getIconField('password', '{#password#}','icons8-key.png');

        $this->layout->scroll[] = $this->components->getShadowBox($content);

        $this->layout->scroll[] = $this->getComponentSpacer('30');

        $this->layout->scroll[] = $this->getComponentText('By continuing, you agree to the Terms & Conditions. 
        Click to view the Terms & Conditions.',
            array('onclick' => $this->getOnclickOpenAction('terms',false,array(
                'open_popup' => 1,
            )),'style' => 'mplug_introtext')
        );
        
        $this->layout->scroll[] = $this->uiKitButtonFilled('{#continue#}',
            array('onclick' => $this->getOnclickSubmit('connect'))
            );

        //$this->layout->footer[] = $this->getComponentRow($btn,array('style' => 'mplug_btn_row'));
        return $this->layout;
    }



}