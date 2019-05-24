<?php

class rentitMobileeventsSubController extends MobileeventsController {

	private $errors = array();

    /* main listing view */
    public function tab1() {
        $this->data = new StdClass();

        $mode = $this->getConfigParam( 'action_mode' );

        if ( $mode == 'text' ) {
	        $this->renderTextbaseView();
        	return $this->data;
        }
        
        if ($this->getConfigParam( 'action_mode' ) != 'terms_conditions') {
            $this->getHeader(1);
        }

        $this->showContent( 'agents' );
        return $this->data;
    }

    public function tab2() {
        $this->data = new StdClass();
        $this->getHeader(2);
        $this->showContent( 'landlords' );
        return $this->data;
    }

    public function tab3() {
        $this->data = new StdClass();
        $this->getHeader(3);
        $this->showContent( 'tenants' );
        return $this->data;
    }

    public function getHeader($active = 1) {

        $content = array(
            'tab1' => '{#agents#}',
            'tab2' => '{#landlords#}',
            'tab3' => '{#tenants#}'
        );

        $params = array(
            'active' => $active,
            'color_topbar' => '#f5f5f5',
            'color_topbar_hilite' => '#1A1C37',
            'indicator_mode' => 'fulltab',
            'btn_padding' => '12 10 12 10',
            'padding' => '7 7 7 7',
            'border-radius' => '3',
            'divider' => true,
            'background' => 'black',
        );

        $this->data->header[] = $this->getRoundedTabs($content, $params);

    }

    public function renderTextbaseView() {

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

	    $text = $this->getConfigParam( 'description' );

	    $this->data->scroll[] = $this->getText($text, array('style' => 'rentit_feedback_title'));

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

    }

    public function showContent( $ref_field, $use_custom_field = false ) {

        $this->rewriteActionConfigField('background_color', '#f6f6f6');

        if ( $title = $this->getConfigParam( 'title_' . $ref_field ) ) {
	        $box[] = $this->getRow(array(
		        $this->getText( $title, array( 'style' => 'rentit-faq-title' ) )
	        ), array(
		        'text-align' => 'center'
	        ));
        }

	    $text = $this->getConfigParam( 'description_' . $ref_field );

	    if ( $use_custom_field ) {
		    $text = $this->getConfigParam( $ref_field );
	    }

        if ( empty($text) ) {
            $this->data->scroll[] = $this->getColumn(array(
                $this->getText( '{#no_faqs_yet#}', array( 'style' => 'rentit-faq-answer' ) )
            ), array('style' => 'events-box-shadowbox'));
            return false;
        }

        $text_array = explode(PHP_EOL, $text);

        foreach ($text_array as $entry) {
            
            if ( empty($entry) OR strlen($entry) < 3 ) {
                continue;
            }

            if ( strstr($entry, '^^') ) {
                $entry = str_replace('^^', '', $entry);
                $box[] = $this->getRow(array(
                    $this->getText( $entry, array( 'style' => 'rentit-faq-question' ) )
                ));
            } else {
                $box[] = $this->getRow(array(
                    $this->getText( $entry, array( 'style' => 'rentit-faq-answer' ) )
                ));
            }

        }

        $this->data->scroll[] = $this->getColumn($box,array('style' => 'events-box-shadowbox'));

        return true;
    }

	protected function sendEmail( $rules ) {

		$email = $this->configobj->feedback_email;

		if ( empty($email) ) {
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
				$body .= $this->submitvariables[$var];
				$body .= '<br />';
			}
		}

		Yii::app()->ses->email()
           ->addTo($email)
           ->setFrom( 'info@appzio.com' )
           ->setSubject( $this->configobj->feedback_subject )
           ->setMessageFromString($body, $body)
           ->addReplyTo( 'info@appzio.com' )
           ->setReturnPath( 'info@appzio.com' )
           ->setSubjectCharset('ISO-8859-1')
           ->setMessageCharset('ISO-8859-1')
           ->send();

		return true;
	}

	private function getValidationRules() {
		return array(
			'temp_description' => '{#please_add_your_comment#}',
		);
	}

}