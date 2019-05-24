<?php

namespace packages\actionMregister\themes\adidas\Views;

use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\adidas\Components\Components;

class View extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;


    /* view will always need to have a function called tab1 */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->model->rewriteActionField('background_image_portrait', '');

        if ($this->getData('mode', 'string') == 'close') {
            $this->layout->scroll[] = $this->getComponentText('{#creating_your_account#}', array('style' => 'mreg_general_text'));
            $this->layout->onload[] = $this->getOnclickCompleteAction();
            return $this->layout;
        }

        $image = $this->model->getConfigParam('actionimage1');
        $this->layout->scroll[] = $this->components->HeaderWithImageBgr($image, array(
            'title' => '{#Connect_with_fans#}',
            'icon' => 'adidas_logo.png'
        ));

        /* get the data defined by the controller */
        $fieldlist = $this->getData('fieldlist', 'array');

        //$this->addCalendar();

        foreach ($fieldlist as $field) {
            $this->addField_page1($field);
        }

        $this->layout->scroll[] = $this->getComponentSpacer('50');

        $onclick = new \stdClass();
        $onclick->action = 'open-action';
        $onclick->open_popup = 1;
        $onclick->action_config = $this->model->getActionidByPermaname('termsconditions');

        $this->layout->footer[] = $this->uiKitButtonHollow('{#sign_up#}', array('onclick' => $this->getOnclickRoute(
            'Controller/default/signup',
            true,
            array('mytestid' => 'Flower sent from Default view', 'exampleid' => 393393),
            true
        )), array(
            'margin' => '20 80 20 80'
        ));

        return $this->layout;
    }

    public function addField_page1($field)
    {
        switch ($field) {

            case 'mreg_collect_photo':
                $this->layout->scroll[] = $this->components->getPhotoField('mreg_collect_photo');
                if (isset($this->model->validation_errors['profilepic']) && !empty($this->model->validation_errors['profilepic'])) {
                    $this->layout->scroll[] = $this->getComponentText($this->model->validation_errors['profilepic'], array(), array(
                        'color' => '#FF0200',
                        'text-align' => 'center',
                        'margin' => '0 0 10 0'
                    ));
                }
                break;

            case 'mreg_collect_full_name':
                $content[] = $this->components->getIconField('firstname', '{#first_name#}', '001-people.png');
                $content[] = $this->getComponentDivider();
                $content[] = $this->components->getIconField('lastname', '{#last_name#}', '001-people.png');
                $content[] = $this->getComponentDivider();
                $this->layout->scroll[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto',
                    'margin' => '0 15 0 15'
                ));
                break;

            case 'mreg_collect_phone':
                $content[] = $this->components->getPhoneNumberField($this->getData('current_country', 'string'), 'phone', '{#phone#}', 'padlock.png');
                $this->layout->scroll[] = $this->components->getShadowBox($this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                )));
                break;

            case 'mreg_collect_email':
                $content[] = $this->components->getIconField('email', '{#email#}', '003-mail.png');
                $content[] = $this->getComponentDivider();

                if(!$this->model->getSavedVariable('fb_token')){
                    $content[] = $this->components->getIconField('password', '{#password#}', '002-security.png');
                    $content[] = $this->getComponentDivider();
                    $content[] = $this->components->getIconField('password_again', '{#password_again#}');
                    $content[] = $this->getComponentDivider();
                }

                $this->layout->scroll[] = $this->getComponentColumn($content, array(), array(
                    'margin' => '0 15 0 15',
                    'width' => 'auto'
                ));

                break;
        }
    }

}