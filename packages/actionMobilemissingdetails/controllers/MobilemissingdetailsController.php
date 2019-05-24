<?php

/*
    These are set by the parent class:

    public $output;     // html output
    public $donebtn;    // done button, includes the clock
    public $taskid;     // current task id
    public $token;      // current task token
    public $added;      // unix time when task was added
    public $timelimit;  // task time limit in unix time
    public $expires;    // unix time when task expires (use time() to compare)
    public $clock;      // html for the task timer
    public $configdata; // these are the custom set config variables per task type
    public $taskdata;   // this contains all data about the task
    public $usertaskid; // IMPORTANT: for any action, this is the relevant id, as is the task user is playing, $taskid is the id of the parent
    public $baseurl;    // application baseurl
    public $doneurl;    // full url for marking the task done
*/

class MobilemissingdetailsController extends ArticleController {

    public $data;
    public $show_error = false;
    public $classname = 'details-field';

    public function getMissingFields() {
        $variables_config = $this->getConfigParam( 'required_variables' );

        if ( empty($variables_config) ) {
            $this->data->scroll[] = $this->getText( 'Missing required variables configuration', array( 'class' => 'details-field' ) );
        }

        $variables = explode(PHP_EOL, $variables_config);

        if ( isset($this->menuid) AND $this->menuid == 'submit-data' ) {

            $this->handleFormSubmission( $variables );

            if ( !$this->show_error ) {
                $onload = new StdClass();
                $onload->action = 'open-action';
                $onload->sync_open = 1;
                $onload->action_config = $this->getConfigParam( 'main_action_id' );
                $onload->id = 'show-content';

                $this->data->onload[] = $onload;
            }

        }

        foreach ($variables as $variable) {
            $var_data = explode('|', $variable);
            $var_string = $var_data[0];
            $validation_type = rtrim($var_data[1]);

            // Check if the required field is really missing
            if ( !isset($this->varcontent[$var_string]) OR empty($this->varcontent[$var_string]) ) {

                switch ($validation_type) {
                    case 'validate_gender':

                        $listparams['variable'] = 'gender';
                        $listparams['show_separator'] = false;
                        $vardata = array( 'man' => '{#man#}', 'woman' => '{#woman#}' );
                        $this->data->scroll[] = $this->formkitRadiobuttons('{#gender#}', $vardata, $listparams, false);

                        break;
                    
                    default:

                        $args = array(
                            'style' => $this->classname,
                            'hint' => '{#please_input_your_'. $var_string .'#}',
                            'variable' => $var_string,
                            'id' => 'id-' . $var_string,
                        );

                        if ( $validation_type == 'validate_age' ) {
                            $args['input_type'] = 'number';
                            $args['submit_menu_id'] = 'submit-data';
                        }

                        $this->data->scroll[] = $this->getFieldtext('', $args);

                        break;
                }

            }

        }

    }

    public function handleFormSubmission( $variables ) {

        foreach ($variables as $variable) {
            $submit_field_is_valid = false;

            $var_data = explode('|', $variable);
            $var_string = $var_data[0];
            $var_id = $this->getVariableId( $var_string );
            $validation_type = $var_data[1];

            // Check if the required field is really missing
            if ( !isset($this->varcontent[$var_string]) OR empty($this->varcontent[$var_string]) ) {
                if ( !empty($this->submitvariables[$var_id]) ) {
                    
                    $value = $this->submitvariables[$var_id];

                    switch ($validation_type) {
                        case 1:
                        case 'validate_email':
                            
                            if ( $this->validateEmail( $value ) ) {
                                $submit_field_is_valid = true;
                            } else {
                                $this->show_error = true;
                            }

                            break;
                        
                        case 'validate_age':
                            
                            if ( $this->validateAge( $value ) ) {
                                $submit_field_is_valid = true;
                            } else {
                                $this->show_error = true;
                            }

                            break;

                        case 'validate_phone':
                            
                            if ( $this->validatePhone( $value ) ) {
                                $submit_field_is_valid = true;
                            } else {
                                $this->show_error = true;
                            }

                            break;

                        default:

                            if ( $value ) {
                                $submit_field_is_valid = true;
                            } else {
                                $this->show_error = true;   
                            }

                            break;
                    }

                    // $this->classname = 'details-field-error';
                } else {
                    $this->show_error = true;
                }
            }

            if ( $submit_field_is_valid ) {
                // Save the data for this field
                $this->saveVariable( $var_string, $value );
            }

        }

    }

    public function validateEmail( $value ) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePhone( $value ) {
        return true;
    }

    public function validateAge( $value ) {

        if ( !is_numeric($value) ) {
            return false;
        }

        $age_options = array(
            'options' => array(
                'min_range' => 18,
                'max_range' => 99
            )
        );

        return ( filter_var( $value, FILTER_VALIDATE_INT, $age_options ) ? true : false );
    }

}