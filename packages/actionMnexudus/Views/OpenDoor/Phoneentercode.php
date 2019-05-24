<?php

namespace packages\actionMnexudus\Views\OpenDoor;

use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Phoneentercode extends BootstrapView
{

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();

        /* this will get called only once */
        $verify = $this->getData('send_verification', 'bool');
        $routing = $this->model->sessionGet('after_verify');

        $this->layout->header[] = $this->components->getBigNexudusHeader('{#verification_code#}', [
                'onclick' => $this->getOnclickOpenSidemenu(),
                'icon' => 'white_hamburger_icon.png'
            ]
        );

        $onfail = $this->getOnclickSubmit('');

        if($routing == 'home'){
            $onsuccess = 'home/verificationdone/';
        } else {
            $onsuccess = 'verification/verificationdone/';
        }

/*        if ($verify) {
            $phone = $this->model->getSavedVariable('opendoor_phone');

            $this->layout->onload[] = $this->getOnclickDoorflowAuthenticate([
                'phone' => $phone,
                'variable_error' => 'authenticate_error',
                'id' => $onsuccess,
                'variable_status' => 'authenticate_status'
            ]);
        }*/

        $phone = $this->model->getSavedVariable('opendoor_phone');

        $col[] = $this->getComponentSpacer(40);

        $col[] = $this->getComponentText('{#you_should_receive_an_sms_shortly#}',
            ['style' => 'nexudus_uikit_formheader']);

        $col[] = $this->getComponentSpacer(10);
        $col[] = $this->getComponentText('{#to_number#}: ' .$phone,
            ['style' => 'nexudus_uikit_formheader']);

        $col[] = $this->getComponentSpacer(10);
        $col[] = $this->getComponentText('{#please_enter_the_code_on_the_field_below#}',
            ['style' => 'nexudus_uikit_formheader']);
        $col[] = $this->getComponentSpacer(40);
        $col[] = $this->components->uiKitGeneralField('code', '{#code#}', 'icon-nexudus-code.png', ['divider' => true, 'input_type' => 'number']);
        $this->layout->scroll[] = $this->getComponentColumn($col, [], ['margin' => '0 20 0 20']);

        $phone = $this->model->getSavedVariable('opendoor_phone');

        $this->layout->footer[] = $this->getComponentSpacer(15);
        $this->layout->footer[] = $this->uiKitButtonFilled('{#cancel_verification#}', [
            'onclick' => [
                $this->getOnclickSubmit('verification/skipverify/'),
                $this->getOnclickDoorflowLogout()
            ]
        ], [
            'background-color' => '#656565',
            'color' => $this->color_top_bar_text_color,
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(15);

        $this->layout->footer[] = $this->uiKitButtonFilled('{#resend_code#}', [
            'onclick' => $this->getOnclickDoorflowAuthenticate([
                'id' => 'verification/entercode/',
                'phone' => $phone,
                'variable_error' => 'authenticate_error',
                'variable_status' => 'authenticate_status',
                'variable_description' => 'authenticate_description'
            ])
        ], [
            'background-color' => '#656565',
            'color' => $this->color_top_bar_text_color,
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(15);
        $this->layout->footer[] = $this->uiKitButtonFilled('{#verify_code#}', [
            'onclick' => [
                $this->getOnclickDoorflowVerify([
                    'variable' => 'code',
                    'phone' => $phone,
                    'id' => 'verification/verificationdone/'
                ])
                ]
        ], [
            'background-color' => $this->color_top_bar_color,
            'color' => $this->color_top_bar_text_color,
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(30);

        return $this->layout;
    }

    public function getDivs()
    {
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }


}
