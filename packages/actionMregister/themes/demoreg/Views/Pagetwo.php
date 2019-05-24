<?php

namespace packages\actionMregister\themes\demoreg\Views;
use packages\actionMregister\Views\Pagetwo as BootstrapView;
use packages\actionMregister\themes\demoreg\Components\Components;

class Pagetwo extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;
    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();

        if($this->getData('mode', 'string') == 'close'){
            $this->layout->scroll[] = $this->getComponentFullPageLoader();
            $this->layout->onload[] = $this->getOnclickCompleteAction();
            return $this->layout;
        }
        
        if($this->model->getSavedVariable('city')){
            $this->layout->scroll[] = $this->getComponentText('{#reg_address_info_was_set#}');
        } else {
            $this->layout->scroll[] = $this->getComponentText('{#reg_address_info_was_not_set#}');
        }

        $this->layout->scroll[] = $this->components->getAddressFields();

        $this->layout->footer[] = $this->uiKitButtonFilled('{#finish_registration#}',array(
            'onclick' => $this->getOnclickSubmit('done')),
            array('margin' => '15 80 15 80')
        );

        return $this->layout;
    }


}