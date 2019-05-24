<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;

class Locationselector extends BootstrapView
{

    /* @var \packages\actionMswipematch\themes\igers\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }


    /* view will always need to have a function called tab1 */
    public function tab1()
    {
        $this->layout = new \stdClass();

        if($this->hide_default_menubar){
            $params['title'] = $this->model->getConfigParam('subject') ? $this->model->getConfigParam('subject') : '{#choose_your_location#}';
            $params['mode'] = 'gohome';
            $params['icon_color'] = $this->color_top_bar_text_color == '#FFFFFFFF' ? 'white' : 'black';

            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }
            $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
        }


        $this->layout->scroll[] = $this->getComponentText('{#please_choose_your_location_by_inputting_an_address#}',[],[
            'margin' => '20 40 20 40',
            'text-align' => 'center'
        ]);


        $addressContent = $this->model->getSavedVariable('address', '{#address#}');

        if ( $this->model->getSubmittedVariableByName('address') ) {
            $addressContent = @json_decode($this->model->getSubmittedVariableByName('address'), true)['address'];
        }

        $open_maps = new \StdClass;
        $open_maps->action = 'choose-google-place';
        $open_maps->custom_dialog = 0;
        $open_maps->variable = $this->model->getVariableId( 'address' );

        if(isset($this->model->validation_errors['address'])){
            $address[] = $this->getComponentText($this->model->validation_errors['address'],array('style' => 'mreg_error'));
        }

        $address[] = $this->getComponentRow( array(
            $this->getComponentImage('placeholder.png', array(
                'style' => 'mreg_icon_field'
            ) ),
            $this->getComponentText( $addressContent, array(
                'variable' => 'address',
            ), array(
                'padding' => '0 0 0 15',
                'font-size' => '14'
            ) )
        ), array(
            'onclick' => $open_maps
        ), array(
            'padding' => '0 0 0 0'
        ) );
        $address[] = $this->getComponentDivider();

        $this->layout->scroll[] = $this->getComponentColumn($address, array(), array(
            'width' => 'auto',
            'margin' => '0 15 0 15',
        ));

        $onclick[] = $this->getOnclickSubmit('locationselector/updateaddress/');
        $onclick[] = $this->getOnclickListBranches();
        $onclick[] = $this->getOnclickGoHome();

        $this->layout->footer[] = $this->getComponentSpacer('20');
        $this->layout->footer[] = $this->uiKitButtonFilled('{#save#}',[
            'onclick' => $onclick
        ]);
        $this->layout->footer[] = $this->getComponentSpacer('20');



        return $this->layout;
    }

}

