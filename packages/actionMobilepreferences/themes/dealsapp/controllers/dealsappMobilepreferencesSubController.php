<?php

class dealsappMobilepreferencesSubController extends MobilepreferencesController {

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '14'
    );

    public function dealsapp(){

        $data = new StdClass();
        $vardata = array();

        $show_error = false;
        $error_empty = false;
        $error_email = false;

        $required = array(
            'real_name|validate-empty',
            // 'phone|validate-empty',
            'email|validate-email',
            'notify|none',
        );

        if(isset($this->menuid) AND $this->menuid == '5555') {
            foreach ($required as $field) {

                $pieces = explode('|', $field);
                $field_name = $pieces[0];
                $field_validation = $pieces[1];

                $varid = $this->getVariableId($field_name);

                switch ($field_validation) {
                    case 'validate-empty':
                        
                        if ( isset($this->submitvariables[$varid]) AND !empty($this->submitvariables[$varid]) ) {
                            $vardata[$field_name] = $this->submitvariables[$varid];
                        } else {
                            $show_error = true;
                            $error_empty = true;
                        }

                        break;

                    case 'validate-email':
                        
                        if ( !isset($this->submitvariables[$varid]) OR empty($this->submitvariables[$varid]) ) {
                            $show_error = true;
                            $error_empty = true;
                        } else if ( !$this->validateEmail( $this->submitvariables[$varid] ) ) {
                            $show_error = true;
                            $error_email = true;
                        } else {
                            $vardata[$field_name] = $this->submitvariables[$varid];
                        }

                        break;
                    
                    default:
                        $vardata[$field_name] = $this->submitvariables[$varid];
                        break;
                }

            }

            AeplayVariable::saveVariablesArray($vardata,$this->playid,$this->gid,'normal');
            $this->loadVariableContent();
        }

        $output[] = $this->getText('Contact Information', array('style' => 'settings_title'));
        $output[] = $this->getText( '', array( 'style' => 'row-divider-preferences' ) );

        $output[] = $this->addField_imate('real_name','Name','Your real name');
        // $output[] = $this->addField_imate('phone','Phone','Your Phone');
        $output[] = $this->addField_imate('email','Email','Your Email');

        $output[] = $this->getCheckbox('notify', 'Enable notifications', false, $this->fontstyle);

        if ( $error_empty ) {
            $output[] = $err = $this->getText('Please fill all fields', array( 'style' => 'register-text-step-error'));
        }

        if ( $error_email ) {
            $output[] = $err = $this->getText('Please enter a valid email', array( 'style' => 'register-text-step-error'));
        }

        $output[] = $this->getSpacer(30);

        if ( isset($this->menuid) AND $this->menuid == '5555' ) {
            $text = ( $show_error ? 'Save' : 'Information saved!' );
            $data->footer[] = $this->getTextbutton($text,array('id' => 5555));
        } else {
            $data->footer[] = $this->getTextbutton('Save',array('id' => 5555));
        }

        $data->scroll = $output;

        $this->data = $data;

        return true;
    }

    public function validateEmail($email){
        $validator = new CEmailValidator;
        $validator->checkMX = true;

        if($email) {
            if ($validator->validateValue($email)) {
                return 1;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}