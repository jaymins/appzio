<?php

class rentitMobilefeedbackSubController extends MobilefeedbackController {

	private $errors = array();

    public function tab1(){

        $this->data = new StdClass();

        if ( isset($this->menuid) AND $this->menuid == 'submit-form' ) {

        	$rules = $this->getValidationRules();

	        foreach ( $rules as $var => $validation_rule ) {
		        if ( empty($this->submitvariables[$var]) ) {
			        $this->errors[] = $validation_rule;
		        }
        	}

        	if ( empty($this->errors) ) {
		        $this->sendEmail( $rules );
	        }

        }

        $this->data->scroll[] = $this->getText('{#please_send_us_feedback_or_question_using_the_form_below#}', array('style' => 'rentit_feedback_title'));

	    $this->getDistrictField();

	    $this->data->scroll[] = $this->getPropertyInput('temp_num_bedrooms', '{#bedrooms#}', 'rentit_feedback_input_row', 'icon-bed.png', '0 0 0 10');

	    $this->getPropertyType();

	    $this->data->scroll[] = $this->getPropertyInput('temp_price', '{#price_per_month#}', 'rentit_feedback_input_row');
	    $this->data->scroll[] = $this->getRow(array(
		    $this->getFieldtextarea('', array('style' => 'mobileproperty_textarea', 'hint' => '{#your_message#}', 'variable' => 'temp_description'))
	    ), array('style' => 'mobileproperty_input'));


	    if ( $this->errors ) {
		    foreach ( $this->errors as $error ) {
			    $this->data->footer[] = $this->getText($error, array('style' => 'rentit_feedback_error'));
		    }
	    }

	    if ( isset($this->menuid) AND $this->menuid == 'submit-form' ) {
            $text = ( $this->errors ? '{#send_feedback#}' : '{#thank_you#}!' );
            $this->data->footer[] = $this->getTextbutton(strtoupper($text),array('id' => 'submit-form', 'style' => 'submit-button'));
        } else {
            $this->data->footer[] = $this->getTextbutton(strtoupper('{#send#}'),array('id' => 'submit-form', 'style' => 'submit-button'));
        }
        
        return $this->data;
    }

    protected function sendEmail( $rules ) {

        $mail = new YiiMailMessage;

        $send_to = $this->configobj->feedback_email;
        $emails = explode(',', $send_to);

        if ( empty($emails) ) {
            return false;
        }

        $name = $this->getSavedVariable('name') ? $this->getSavedVariable('name') : $this->getSavedVariable('real_name');

        $body = 'User name: ' . $name;
        $body .= '<br />';
        $body .= 'Email: ' . $this->getSavedVariable('email');
        $body .= '<br />';
        $body .= 'OS: ' . $this->getSavedVariable('system_push_plattform');
        $body .= '<br />';
        $body .= 'Play ID: ' . $this->playid;
        $body .= '<br />';
        $body .= 'Message: ';
        $body .= '<br /><br />';

	    foreach ( $rules as $var => $rule ) {
			if ( isset($this->submitvariables[$var]) AND $this->submitvariables[$var] ) {
				$field_label = str_replace('temp_', '', $var);
				$field_label = str_replace('_', ' ', $field_label);
				$body .= ucfirst($field_label) . ': ' . $this->submitvariables[$var];
				$body .= '<br />';
			}
        }

        $mail->setBody($body, 'text/html');
        $mail->addTo( $emails[0] );

        foreach ($emails as $i => $email) {
            // Skip the first email
            if ( !$i ) {
                continue;
            }
            $mail->AddBCC( $email );
        }

        // $mail->AddBCC( 'spmitev@gmail.com' );
        $mail->from = array('info@appzio.com' => 'Appzio');
        $mail->subject = $this->configobj->feedback_subject;

        Yii::app()->mail->send($mail);

        return true;
    }

	public function getPropertyInput($variable, $hint, $style = 'mobileproperty_input', $image = null, $imageOffset = '10 0 0 10') {
		$data = array();

		if ( !is_null($image) ) {
			$data[] = $this->getImage($image, array('width' => '20', 'margin' => $imageOffset));
		}

		$params = array('style' => 'mobileproperty_textfield', 'hint' => $hint, 'variable' => $variable);

		$number_vars = array(
			'temp_square_ft', 'temp_price_per_month', 'temp_num_bedrooms', 'temp_num_bathrooms', 'temp_area', 'temp_price',
		);

		if ( in_array($variable, $number_vars) ) {
			$params['input_type'] = 'number';
		}

		if ( $variable == 'temp_name' ) {
			$params['input_type'] = 'name';
		}

		$data[] = $this->getFieldtext('', $params);
		return $this->getRow($data, array('style' => $style));
	}

	public function getDistrictField() {

		$submitted_district = '';
		$district_text = '{#area_/_district#}';

		$onclick = new StdClass();
		$onclick->action = 'open-action';
		$onclick->action_config = $this->getActionidByPermaname('selectpropertylocation');
		$onclick->id = 'tmp-property';
		$onclick->open_popup = 1;
		$onclick->sync_open = 1;
		$onclick->back_button = 1;
		$onclick->keep_user_data = 1;

		$data = array();
		$data[] = $this->getText($district_text, array(
			'variable' => 'temp_district',
			'style' => 'mobileproperty_textbutton'
		));

		$this->data->scroll[] = $this->getRow($data, array('style' => 'mobileproperty_input','onclick' => $onclick));
		$this->data->scroll[] = $this->getFieldtext($submitted_district, array(
			'variable' => 'temp_district',
			'opacity' => '0',
			'height' => '1',
		));
	}

	private function getPropertyType() {
		$propertyOptions = array(
			'room' => ucfirst('{#room#}'),
			'flat' => ucfirst('{#flat#}'),
			'house' => ucfirst('{#house#}')
		);

		$field_options = array(
			'show_separator' => false,
			'variable' => 'temp_property_type',
			'field_offset' => 3
		);

		$propertyType[] = $this->formkitRadiobuttons('{#property_type#}', $propertyOptions, $field_options);
		$this->data->scroll[] = $this->getRow($propertyType, array(
			'margin' => '10 10 0 10',
			'background-color' => '#FFFFFF',
			'shadow-color' => '#33000000',
			'shadow-radius' => 1,
			'shadow-offset' => '0 1',
			'border-radius' => '3',
			'padding' => '10 10 10 0'
		));
	}

	private function getValidationRules() {
    	return array(
		    'temp_district' => '{#please_choose_a_district#}',
		    'temp_num_bedrooms' => '{#please_enter_bedrooms_count#}',
		    'temp_property_type' => '{#please_select_a_property_type#}',
		    'temp_price' => '{#please_enter_a_price#}',
	        'temp_description' => '{#please_add_your_comment#}',
	    );
	}

}