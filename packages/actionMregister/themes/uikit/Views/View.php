<?php

namespace packages\actionMregister\themes\uikit\Views;

use packages\actionMregister\themes\uikit\Components\Components;
use packages\actionMregister\Views\View as BootstrapView;

class View extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1()
    {
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $this->layout = new \stdClass();

        $cache = \Appcaching::getGlobalCache('location-asked' . $this->model->playid);

        if (!$cache) {
            $buttonparams = new \StdClass();
            $buttonparams->action = 'submit-form-content';
            $buttonparams->id = 'save-variables';
            $this->layout->onload[] = $buttonparams;

            $menu2 = new \StdClass();
            $menu2->action = 'ask-location';
            $this->layout->onload[] = $menu2;
            \Appcaching::setGlobalCache('location-asked' . $this->model->playid, true, 43200);
        }

        if (!$this->model->getConfigParam('hide_header')) {

            if ($this->model->getConfigParam('actionimage1')) {
                $image = $this->model->getConfigParam('actionimage2');
                $this->layout->scroll[] = $this->getComponentRow(array(
                    $this->getComponentImage($image, array(), array()),
                ), array(), array(
                    'text-align' => 'right',
                    'background-color' => $this->color_top_bar_color,
                ));
            } else {
                $image = 'register-dhl-logo.png';
                $this->layout->scroll[] = $this->getComponentRow(array(
                    $this->getComponentImage($image, array(), array(
                        'width' => '120'
                    )),
                ), array(), array(
                    'padding' => '15 15 0 15',
                    'text-align' => 'right',
                    'background-color' => $this->color_top_bar_color,
                ));
            }

            if ($this->model->getConfigParam('actionimage1')) {
                $image = $this->model->getConfigParam('actionimage1');
            } else {
                $image = 'dhl_app_icon.png';
            }

            $text = '{#register_header_text#}';

            $this->layout->scroll[] = $this->getComponentColumn(array(
                $this->getHeaderImage($image),
                $this->getHeaderText($text)
            ), [], array(
                'background-color' => $this->color_top_bar_color,
                'text-align' => 'center'
            ));
        }

        $this->renderNavigation();
        $this->renderRegisterFields();

        $this->renderRegisterButton();
        $this->layout->footer[] = $this->uiKitTermsText(['actionid' => $this->model->getConfigParam('terms_action')]);

        return $this->layout;
    }

    protected function getHeaderImage($image)
    {
        return $this->getComponentImage($image, [], array(
            'width' => '200',
            'margin' => '20 0 20 0'
        ));
    }

    protected function getHeaderText($text)
    {
        return $this->getComponentText($text, [], array(
            'color' => '#ffffff',
            'font-size' => '24',
            'text-align' => 'center',
            'margin' => '0 0 50 0',
        ));
    }


    protected function renderNavigation()
    {
        $this->layout->scroll[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#sign_in#}'),
                'active' => false,
                'onclick' => $this->getOnclickOpenAction('login', false, array('transition' => 'none'))
            ),
            array(
                'text' => strtoupper('{#sign_up#}'),
                'active' => true,
                'onclick' => new \stdClass()
            )
        ));
    }

    protected function renderRegisterFields()
    {
        if ($this->getData('mode', 'string') == 'close') {
            $this->layout->scroll[] = $this->getComponentText('{#creating_your_account#}', array('style' => 'mreg_general_text'));
            $this->layout->onload[] = $this->getOnclickCompleteAction();
            return $this->layout;
        }

        $fieldlist = $this->getData('fieldlist', 'array');

        foreach ($fieldlist as $field) {
            $this->addField_page1($field);
        }

        return true;
    }

    protected function renderRegisterButton()
    {
        $this->layout->footer[] = $this->uiKitWideButton(strtoupper('{#create_account#}'), array(
            'onclick' => $this->getOnclickRoute(
                'Controller/default/signup',
                true
            )
        ), array(
            'margin' => '10 0 0 0',
            'background-color' => $this->color_top_bar_color,
            'color' => $this->color_top_bar_text_color
        ));
    }

    public function addField_page1($field)
    {
        switch ($field) {

            case 'mreg_collect_photo':
                $col[] = $this->components->getPhotoField('mreg_collect_photo');
                break;

            case 'mreg_collect_full_name':
                $col[] = $this->components->uiKitGeneralField('firstname', '{#first_name#}', 'icon-user.png');
                $col[] = $this->components->uiKitDivider();
                $col[] = $this->components->uiKitGeneralField('lastname', '{#last_name#}');
                $col[] = $this->components->uiKitDivider();
                break;

            case 'mreg_collect_nickname':
                $col[] = $this->components->uiKitGeneralField('nickname', '{#nickname#}', 'icon-user.png');
                $col[] = $this->components->uiKitDivider();
                break;

            case 'mreg_collect_phone':
                $content[] = $this->components->getPhoneNumberField($this->getData('current_country', 'string'), 'phone', '{#phone#}', 'mreg-icon-phone.png');
                $col[] = $this->components->getShadowBox($this->getComponentColumn($content, array(), array(
                    'width' => '100%'
                )));
                break;

            case 'mreg_collect_email':

                $col[] = $this->components->uiKitGeneralField('email', '{#email#}', 'mail3.png');
                $col[] = $this->components->uiKitDivider();
                $col[] = $this->components->uiKitGeneralField('password', '{#password#}', 'key2.png');
                $col[] = $this->components->uiKitDivider();
                $col[] = $this->components->uiKitGeneralField('password_again', '{#password_again#}', 'key2.png');
                $col[] = $this->components->uiKitDivider();

                break;
        }

        if (isset($col)) {
            $this->layout->scroll[] = $this->getComponentColumn($col, [], array('margin' => '0 20 0 20'));
        }

    }

}