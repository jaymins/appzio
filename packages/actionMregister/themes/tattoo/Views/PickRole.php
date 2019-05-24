<?php

namespace packages\actionMregister\themes\tattoo\Views;
use packages\actionMregister\Views\Pagetwo as BootstrapView;
use packages\actionMregister\themes\example\Components\Components;

class PickRole extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;
    public $tab;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentImage('tatjack-icon-wheel.png', array(), array(
                'width' => '280',
                'margin' => '50 0 50 0'
            ))
        ), array(), array(
            'text-align' => 'center'
        ));

        $this->layout->footer[] = $this->getComponentRow(array(
            $this->getComponentImage('user-icon.png', array(), array(
                'margin' => '0 20 0 0'
            )),
            $this->getComponentText('{#i_want_to_get_a#}' . ' ' . '{#tattoo#}', array(), array(
                'color' => '#adadad',
                'font-size' => '18',
                'font-weight' => 'bold'
            )),
            $this->getComponentImage('arrow_orange.png', array(), array(
                'width' => '10',
                'floating' => true,
                'float' => 'right'
            ))
        ), array(
            'onclick' => $this->getOnclickSubmit('set_role_user')
        ), array(
            'border-color' => '#141414',
            'border-radius' => '20',
            'color' => '#adadad',
            'padding' => '12 20 13 20',
            'margin' => '0 20 10 20',
            'vertical-align' => 'middle',
            'background-color' => '#141414'
        ));

        $this->layout->footer[] = $this->getComponentRow(array(
            $this->getComponentImage('artist-icon.png', array(), array(
                'margin' => '0 18 0 0'
            )),
            $this->getComponentText('{#i_create#}' . ' ' . '{#tattoos#}', array(), array(
                'color' => '#ffffff',
                'font-size' => '18',
                'font-weight' => 'bold',
            )),
            $this->getComponentImage('arrow.png', array(), array(
                'width' => '10',
                'floating' => true,
                'float' => 'right'
            ))
        ), array(
            'onclick' => $this->getOnclickSubmit('set_role_artist')
        ), array(
            'border-color' => '#141414',
            'border-radius' => '20',
            'padding' => '12 20 13 20',
            'margin' => '0 20 10 20',
            'vertical-align' => 'middle'
        ));

        $this->layout->footer[] = $this->getComponentSpacer('30');

        return $this->layout;
    }

}