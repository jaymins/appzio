<?php

namespace packages\actionMregister\themes\tattoo\Views;

use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\example\Components\Components;

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

        if ($this->getData('mode', 'string') == 'close') {
	        $this->layout->scroll[] = $this->getComponentText('{#creating_your_account#}', array('style' => 'mreg_general_text'));
            $this->layout->onload[] = $this->getOnclickCompleteAction();
            return $this->layout;
        }

        /* get the data defined by the controller */
        $fieldlist = $this->getData('fieldlist', 'array');

        //$this->addCalendar();

        foreach ($fieldlist as $field) {
            $this->addField_page1($field);
        }

        if ($this->model->getSavedVariable('role') === 'artist') {

	        $addressContent = empty( $this->model->getSavedVariable( 'address' ) ) ?
		        'Address' : $this->model->getSavedVariable( 'address' );

	        $open_maps = new \StdClass;
	        $open_maps->action = 'choose-google-place';
	        $open_maps->custom_dialog = 0;
	        $open_maps->variable = $this->model->getVariableId( 'address' );

	        if(isset($this->model->validation_errors['address'])){
		        $address[] = $this->getComponentText($this->model->validation_errors['address'],array('style' => 'mreg_error'));
	        }

	        $address[] = $this->getComponentRow( array(
		        $this->getComponentImage( 'tatjack-icon-address.png', array(
			        'style' => 'mreg_icon_field'
		        ) ),
		        $this->getComponentText( $addressContent, array(
			        'variable' => 'address',
		        ), array(
			        'color'  => '#ffffff',
			        'margin' => '0 0 0 20',
		        ) )
	        ), array(
		        'onclick' => $open_maps
	        ), array(
		        'padding' => '0 0 0 20'
	        ) );
	        $address[] = $this->getComponentDivider();

            $this->layout->scroll[] = $this->getComponentColumn($address, array(), array(
                'width' => 'auto',
                'margin' => '0 20 0 20',
            ));

            $phone[] = $this->components->getIconField('phone', '{#phone#}', 'tatjack-icon-phone.png');
            $phone[] = $this->getComponentDivider();

            $this->layout->scroll[] = $this->getComponentColumn($phone, array(), array(
                'width' => 'auto',
                'margin' => '10 20 0 20',
            ));

            $price[] = $this->components->getIconField(
                'price',
                '{#price_per_hour#}',
                'dollar_sign_new.png',
                array('input_type' => 'number'));
            $price[] = $this->getComponentDivider();

            $this->layout->scroll[] = $this->getComponentColumn($price, array(), array(
                'width' => 'auto',
                'margin' => '10 20 0 20',
            ));
        }

        $this->layout->scroll[] = $this->getComponentSpacer('50');

        $btn[] = $this->getComponentText(strtoupper('{#sign_up#}'), array(
            'onclick' => $this->getOnclickRoute(
                'Controller/default/signup',
                true,
                array('mytestid' => 'Flower sent from Default view', 'exampleid' => 393393),
                true
            )), array(
                'border-color' => '#141414',
                'border-radius' => '20',
                'background-color' => '#141414',
                'text-align' => 'center',
                'color' => '#ffffff',
                'padding' => '12 20 13 20',
                'margin' => '0 20 10 20',
                'font-size' => '18',
                'font-weight' => 'bold',
                'width' => '80%'
        ));

        $onclick = new \stdClass();
        $onclick->action = 'open-action';
        $onclick->open_popup = 1;
        $onclick->action_config = $this->model->getActionidByPermaname('termsconditions');

        if (isset($this->model->validation_errors['terms'])) {
            $this->layout->scroll[] = $this->getComponentText($this->model->validation_errors['terms'], array(), array(
                'color' => '#ff0000',
                'text-align' => 'center',
                'margin' => '0 0 10 0'
            ));
        }

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentFormFieldOnoff(array(
                'variable' => 'terms'
            ), array(
                'margin' => '0 15 0 0'
            )),
            $this->getComponentText('{#i_agree_with_the_#}', array(), array(
                'margin' => '0 0 0 0',
                'padding' => '0 0 0 0'
            )),
            $this->getComponentText('{#terms_&_conditions#}', array(
                'onclick' => $onclick
            ), array(
                'margin' => '0 0 0 0',
                'padding' => '0 0 0 0',
                'font-weight' => 'bold'
            ))
        ), array(), array(
            'text-align' => 'center',
            'margin' => '0 0 15 0'
        ));

        $this->layout->scroll[] = $this->getComponentRow($btn, array('style' => 'mreg_btn_row'));
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
                $content[] = $this->components->getIconField('firstname', '{#first_name#}', 'tatjack-icon-profile.png');
                $content[] = $this->getComponentDivider();
                $content[] = $this->components->getIconField('lastname', '{#last_name#}');
                $content[] = $this->getComponentDivider();
                $this->layout->scroll[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto',
                    'margin' => '0 20 0 20'
                ));
                break;

            case 'mreg_collect_phone':
                $content[] = $this->components->getPhoneNumberField($this->getData('current_country', 'string'), 'phone', '{#phone#}', 'tatjack-icon-phone.png');
                $this->layout->scroll[] = $this->components->getShadowBox($this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                )));
                break;

            case 'mreg_collect_email':
                $content[] = $this->components->getIconField('email', '{#email#}', 'tatjack-icon-mail.png');
                $content[] = $this->getComponentDivider();
                $content[] = $this->components->getIconField('password', '{#password#}', 'tatjack-icon-pass.png');
                $content[] = $this->getComponentDivider();
                $content[] = $this->components->getIconField('password_again', '{#password_again#}');
                $content[] = $this->getComponentDivider();
                $this->layout->scroll[] = $this->getComponentColumn($content, array(), array(
                    'margin' => '0 20 0 20',
                    'width' => 'auto'
                ));

                break;
        }
    }

}