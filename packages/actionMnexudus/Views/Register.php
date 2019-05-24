<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Register extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->header[] = $this->components->getBigNexudusHeader('{#register#}');

        /* form fields */
        $col[] = $this->components->uiKitGeneralField('firstname', '{#first_name#}', 'icon-nexudus-user.png',['divider' => true]);
        $col[] = $this->components->uiKitGeneralField('lastname', '{#last_name#}', 'icon-nexudus-user.png',['divider' => true]);
        $col[] = $this->components->uiKitGeneralField('phone', '{#phone#}', 'icon-nexudus-phone.png',['divider' => true]);
        $col[] = $this->components->uiKitGeneralField('email', '{#email#}', 'icon-nexudus-envelope.png',['divider' => true]);
        $col[] = $this->components->uiKitGeneralField('password', '{#password#}', 'icon-nexudus-lock.png',['divider' => true]);
        $col[] = $this->components->uiKitGeneralField('repeat_password', '{#repeat_password#}', 'icon-nexudus-lock.png',['divider' => true]);
        $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '0 15 30 15']);

        $this->checkBoxes();

        $this->layout->footer[] = $this->uiKitButtonFilled('{#register#}',[
            'onclick' => $this->getOnclickSubmit('register')
        ],[
            'background-color' => $this->color_top_bar_color,
            'color' => $this->color_top_bar_text_color,
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(15);


        return $this->layout;
    }
    
    public function checkBoxes(){
        $col[] = $this->getCheckBox('terms');
        $col[] = $this->getComponentText('{#by_clicking_this_you_agree_to_terms#}',['style' => 'nexudus_checkbox_text'],[]);
        $this->layout->scroll[] = $this->getComponentRow($col,[
            'onclick' => $this->getOnclickOpenAction('terms',false,['open_popup' => 1])
        ],['vertical-align' => 'middle','margin' => '0 0 15 0']);

        if(isset($this->model->validation_errors['terms'])){
            $output[] = $this->uiKitDividerError();
            $output[] = $this->uiKitFormErrorText($this->model->validation_errors['terms']);
            $this->layout->scroll[] = $this->getComponentColumn($output,[],['margin' => '0 29 10 29']);
            unset($output);
        }

        unset($col);
        $col[] = $this->getCheckBox('over16');
        $col[] = $this->getComponentText('{#i_am_over_sixteen_years_old#}',['style' => 'nexudus_checkbox_text'],[]);
        $this->layout->scroll[] = $this->getComponentRow($col,[],['vertical-align' => 'middle','margin' => '0 0 15 0']);

        if(isset($this->model->validation_errors['over16'])){
            $output[] = $this->uiKitDividerError();
            $output[] = $this->uiKitFormErrorText($this->model->validation_errors['over16']);
            $this->layout->scroll[] = $this->getComponentColumn($output,[],['margin' => '0 29 10 29']);
        }

        unset($col);
        $col[] = $this->getCheckBox('promo');
        $col[] = $this->getComponentText('{#i_would_like_to_receive_promotional#}',['style' => 'nexudus_checkbox_text'],[]);
        $this->layout->scroll[] = $this->getComponentRow($col,[],['vertical-align' => 'middle','margin' => '0 0 15 0']);
    }

    public function getCheckBox($variable){
        $selected = $this->getImageFileName('icon-nexudus-checkbox-white-checked.png');
        $unselected = $this->getImageFileName('icon-nexudus-checkbox-white.png');

        $selectstate = array(
            'style_content' => $this->checkBoxStyle($selected),
            'variable_value' => 1,
            'allow_unselect' => 1,
            'variable' => $variable,
            'animation' => 'fade'
        );

        if($this->model->getSubmittedVariableByName($variable) == 1 OR $this->model->getSavedVariable($variable) == 1){
            $selectstate['active'] = '1';
        } else {
            $selectstate['active'] = false;
        }

        return
            $this->getComponentText('', array(
                'variable' => $variable,
                'variable_value' => 1,
                'style_content' => $this->checkBoxStyle($unselected),
                'allow_unselect' => 1,
                'selected_state' => $selectstate,
            ));

    }

    private function checkBoxStyle($bg){
        $style = new \stdClass();
        $style->width = '26';
        $style->height = '26';
        $field_name = 'background-image';
        $style->$field_name = $bg;
        $field_name = 'background-size';
        $style->$field_name = 'cover';
        $style->margin = '0 22 0 29';
        return $style;
    }


    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }



}
