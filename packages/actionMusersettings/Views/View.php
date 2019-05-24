<?php

namespace packages\actionMusersettings\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMusersettings\Controllers\Components;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView {

    /* @var \packages\actionMusersettings\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
//        $this->setTopShadow();

        $this->model->rewriteActionConfigField('hide_menubar', 1);

        $btn_onclick[] = $this->components->getOnclickSubmit('controller/save/');
        $btn_onclick[] = $this->getOnclickListBranches();

        $this->layout->header[] = $this->components->getFauxTopbar(array(
            'mode' => 'gohome','btn_title'=>'{#save#}','title' => '{#user_settings#}','btn_onclick' => $btn_onclick));

        /* get the data defined by the controller */
        $fieldList = $this->getData('fieldlist','array');

        $collection = [];
        foreach($fieldList as $field){

            $groupName = explode('_', $field);

            if ($groupName[0] == 'mreg' && $groupName[1] != 'collect') {
                if (!isset($collection[$groupName[1]])) {
                    $collection[$groupName[1]] = [];
                }
                $collection[$groupName[1]] = array_merge($collection[$groupName[1]], $this->addField_page1($field));
            }
        }

        foreach ($collection as $title => $items) {

            $this->layout->scroll[] = $this->components->getSummaryBox($title, $items);
        }

        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */

    public function getDivs(){
        $divs = new \stdClass();

        /* look for traits under the components */
        $divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }

    public function addField_page1($field){
        $result = [];
        switch($field){

            case 'mreg_profile_collect_photo':
                $result[] = $this->components->getPhotoField('mreg_collect_photo');
                $result[] = $this->getDivider();
                break;

            case 'mreg_profile_collect_full_name':
                $result[] = $this->components->getIconField('firstname','{#first_name#}','mreg-icon-person.png');
                $result[] = $this->getDivider();
                $result[] = $this->components->getIconField('lastname','{#last_name#}');
                $result[] = $this->getDivider();
                break;

            case 'mreg_profile_collect_phone':
                $result[] = $this->components->getPhoneNumberField($this->getData('current_country','string'),'phone','{#phone#}','mreg-icon-phone.png');
                $result[] = $this->getDivider();
                break;


            case 'mreg_profile_collect_email':
                $result[] = $this->components->getIconField('email','{#email#}','mreg-icon-mail.png');
                $result[] = $this->getDivider();

                break;

            case 'mreg_password_collect_password':
                $result[] = $this->components->getIconField('password_current','{#current_password#}');
                $result[] = $this->getDivider();
                $result[] = $this->components->getIconField('password','{#password#}','mreg-icon-key.png');
                $result[] = $this->getDivider();
                $result[] = $this->components->getIconField('password_again','{#password_again#}');
                $result[] = $this->getDivider();

        }

        return $result;
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


}
