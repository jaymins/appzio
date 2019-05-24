<?php

namespace packages\actionMnexudus\Views\OpenDoor;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Phone extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();
        $can_skip = $this->getData('can_skip', 'bool');

        $this->layout->header[] = $this->components->getBigNexudusHeader('{#phone_verification#}',[
                'onclick' => $this->getOnclickOpenSidemenu(),
                'icon' => 'white_hamburger_icon.png'
            ]
        );

        $col[] = $this->getComponentSpacer(40);
        $col[] = $this->getComponentText('{#we_need_to_verify_your_phone_number#}',
            ['style' => 'nexudus_uikit_formheader']);
        $col[] = $this->getComponentSpacer(10);
        $col[] = $this->getComponentText('{#please_input_your_mobile_number_with_country_code#}',
            ['style' => 'nexudus_uikit_formheader']);
        $col[] = $this->getComponentSpacer(40);


        if($this->model->getSubmittedVariableByName('phone') AND strlen($this->model->getSubmittedVariableByName('phone')) > 3){
            $phone = $this->model->getSubmittedVariableByName('phone');
        } elseif($this->model->getSavedVariable('phone') AND strlen($this->model->getSavedVariable('phone')) > 3){
            $phone = $this->model->getSavedVariable('phone');
        } else {
            $phone = '';
        }

        if(substr($phone,0,3) == '+44'){
            $phone = substr($phone,3);
        }

        $row[] = $this->getComponentImage('icon-nexudus-phone.png',[],['width' => '20']);
        $row[] = $this->getComponentText('0044-',[],['color' => '#ffffff','opacity' => '0.6']);
        $row[] = $this->getComponentFormFieldText($phone,[
            'activation'=>'initially','variable'=>'phone',
            'input_type' => 'phone'
            ],['color' => '#ffffff']);

        $col[] = $this->getComponentRow($row,[],['vertical-align' => 'middle','text-align' => 'left']);
        $col[] = $this->getComponentDivider();

        $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '0 40 0 40']);

        if($can_skip){
            $this->layout->footer[] = $this->getComponentSpacer(15);
            $this->layout->footer[] = $this->uiKitButtonFilled('{#skip#}',[
                'onclick' => $this->getOnclickSubmit('verification/skipverify/')
            ],[
                'background-color' => $this->color_top_bar_color,
                'color' => $this->color_top_bar_text_color,
            ]);
        }

        $this->layout->footer[] = $this->getComponentSpacer(15);
        $this->layout->footer[] = $this->uiKitButtonFilled('{#verify#}',[
            'onclick' => $this->getOnclickSubmit('verification/verifyphone/doverify')
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
