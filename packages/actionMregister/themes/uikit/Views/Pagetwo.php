<?php

namespace packages\actionMregister\themes\uikit\Views;

use packages\actionMregister\Views\Pagetwo as BootstrapView;
use packages\actionMregister\themes\uikit\Components\Components;

class Pagetwo extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;
    public $tab;


    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        if(!$this->model->getConfigParam('hide_header')) {

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

            $text = '{#authentication_code#}';

            $this->layout->scroll[] = $this->getComponentColumn(array(
                $this->getHeaderImage($image),
                $this->getHeaderText($text)
            ), [], array(
                'background-color' => $this->color_top_bar_color,
                'text-align' => 'center'
            ));
        }

        $this->renderNavigation();

        $this->layout->scroll[] = $this->getComponentText('{#enter_the_authentication_code_sent_to_your_email#}', array(), array(
            'text-align' => 'center',
            'color' => '#939a9a',
            'margin' => '40 40 20 40'
        ));

        $field[] = $this->components->getIconField('code','{#authentication_code#}','icon-key.png', array(
            'input_type' => 'number'
        ));

        $field[] = $this->getDivider();

        $this->layout->scroll[] = $this->getComponentColumn($field, array(), array(
            'margin' => '0 20 0 20',
            'background-color' => '#ffffff'
        ));

        $this->renderRegisterButton();

        return $this->layout;
    }

    protected function getHeaderImage($image)
    {
        return $this->getComponentImage($image,[],array(
            'width' => '200',
            'margin' => '20 0 20 0'
        ));
    }

    protected function getHeaderText($text)
    {
        return $this->getComponentText($text, [],array(
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
                'onclick' => $this->getOnclickOpenAction('login',false,array('transition' => 'none'))
            ),
            array(
                'text' => strtoupper('{#sign_up#}'),
                'active' => true,
                'onclick' => new \stdClass()
            )
        ));
    }
    protected function renderRegisterButton()
    {

        $this->layout->footer[] = $this->uiKitWideButton(strtoupper('{#create_account#}'), array(
            'onclick' => $this->getOnclickSubmit('done')
        ),array('background-color' => $this->colors['button_color']));

    }

}