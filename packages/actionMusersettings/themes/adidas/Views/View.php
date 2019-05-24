<?php

namespace packages\actionMusersettings\themes\adidas\Views;
use packages\actionMusersettings\Views\View as BootstrapView;
use packages\actionMusersettings\themes\adidas\Components\Components;

class View extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){

        $this->layout = new \stdClass();

        $this->displayFieldList();

        $this->displaySaveButton('footer');

        return $this->layout;

    }

    public function addField_page1($field){
        $result = [];
        switch($field){

            case 'mreg_profile_collect_photo':
                $content[] = $this->components->getPhotoField('mreg_collect_photo');
                $content[] = $this->getDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'margin' => '0 15 0 15',
                    'width' => 'auto'
                ));
                break;

            case 'mreg_profile_collect_full_name':
                $content[] = $this->components->getIconField('firstname', '{#first_name#}', 'avatar.png');
                $content[] = $this->getComponentDivider();
                $content[] = $this->components->getIconField('lastname', '{#last_name#}', 'avatar.png');
                $content[] = $this->getComponentDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto',
                    'margin' => '0 15 0 15'
                ));
                break;

            case 'mreg_profile_collect_email':
                $content[] = $this->components->getIconField('email', '{#email#}', 'envelope.png');
                $content[] = $this->getComponentDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'margin' => '0 15 0 15',
                    'width' => 'auto'
                ));
                break;

        }

        return $result;
    }

    public function displaySaveButton($location = 'scroll') {
        $saved = $this->getData('saved', 'int');

        if ($saved) {
            $text = strtoupper($this->model->localize('{#saved#}'));
        } else {
            $text = strtoupper($this->model->localize('{#save#}'));
        }

        $onclick[] = $this->getOnclickSubmit('Controller/save/');
        $onclick[] = $this->getOnclickListBranches();

        $this->layout->{$location}[] = $this->getComponentSpacer('20');
        $this->layout->{$location}[] = $this->uiKitButtonHollow($text, array(
            'onclick' => $onclick
        ), array(
            'color' => '#333333',
        ));
        $this->layout->{$location}[] = $this->getComponentSpacer('5');
        $this->layout->{$location}[] = $this->uiKitButtonHollow('Diplay My Profile', array(
            'onclick' => $this->getOnclickSubmit('Profile/default/'. $this->model->playid)
        ), array(
            'color' => '#ffffff',
            'background-color' => '#005496'
        ));

        $this->layout->{$location}[] = $this->getComponentSpacer('20');
    }

    public function displayFieldList() {
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
    }

}