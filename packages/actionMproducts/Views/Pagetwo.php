<?php

namespace packages\actionMproducts\Views;

class Pagetwo extends View {

    /* @var \packages\actionMproducts\Components\Components */
    public $components;
    public $theme;

    /**
     * Pagetwo constructor.
     * @param $obj
     */
    public function __construct($obj){
        parent::__construct($obj);
    }

    /**
     * @return \stdClass
     */
    public function tab1(){
        $this->layout = new \stdClass();

        if($this->getData('mode', 'string') == 'close'){
            $this->layout->scroll[] = $this->getComponentFullPageLoader();
            $this->layout->onload[] = $this->getOnclickCompleteAction();
            return $this->layout;
        }

        $this->layout->scroll[] = $this->getComponentText('Your registration is nearly finished. Just fill these and you are good to go.');
        $fieldlist = $this->getData('fieldlist','array');

        foreach($fieldlist as $field){
            $this->addField_page2($field);
        }

        /* route: Contoller/action. Controller defines the view file. */
        $btn[] = $this->getComponentText('{#finish_registration#}',
            array('style' => 'mreg_btn',
                'onclick' => $this->getOnclickSubmit('done')
            ));

        $this->layout->footer[] = $this->getComponentRow($btn,array('style' => 'mreg_btn_row'));
        return $this->layout;
    }

    /**
     * @return \stdClass
     */
    public function getDivs(){
        $divs = new \stdClass();
        $divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }

    /**
     * @param $field
     */
    public function addField_page2($field){
        switch($field){

            case 'mreg_collect_profile_comment':

                $textarea = $this->components->getComponentFormFieldTextArea('',array(
                    'variable' => $this->model->getVariableId('profile_comment'),
                    'hint' => $this->model->getConfigParam('mreg_hint_text_for_profile_comment')
                ));

                $this->layout->scroll[] = $this->components->getShadowBox($textarea);
                break;

        }
    }

    /**
     * @return \stdClass
     */
    public function getDivider(){
        return $this->getComponentText('',array('style' => 'mreg_divider'));
    }

    /**
     * @return void
     */
    public function setTopShadow(){
        $txt[] = $this->getComponentText('');
        $this->layout->header[] = $this->getComponentRow($txt, array(), array(
            'background-color' => $this->color_top_bar_color,
            'parent_style' => 'mreg_top_shadow'
        ));
    }
}
