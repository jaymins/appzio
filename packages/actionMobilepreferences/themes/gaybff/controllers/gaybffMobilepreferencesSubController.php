<?php

class gaybffMobilepreferencesSubController extends MobilepreferencesController {

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '14'
    );

    public function tab1(){

        if ( $this->getConfigParam( 'gaybff_edit_preferences' ) ) {
            $this->data = new StdClass();
            $this->getVariablesView();
            return $this->data;
        }

        $this->rewriteActionConfigField('background_color', '#F8F8FF');

        $this->data = new StdClass();

        $this->validateAndSave();

        $titlestyle['width'] = '60%';
        $this->data->scroll[] = $this->getText('{#contact#}',array('style' => 'settings_title'));

        $this->data->scroll[] = $this->formkitCheckbox('notify', '{#enable_notifications#}', array(
            'type' => 'toggle',
        ));

        $this->data->scroll[] = $this->getText('{#filtering#}',array('style' => 'settings_title'));

        $this->data->scroll[] = $this->formkitSlider('{#distance#} ({#miles#})','distance','6213',50,6213,10);

        $age_min = ( $this->getVariable( 'filter_age_start' ) ? $this->getVariable( 'filter_age_start' ) : 18 );
        $age_max = ( $this->getVariable( 'filter_age_end' ) ? $this->getVariable( 'filter_age_end' ) : 30 );

        $this->data->scroll[] = $this->formkitSlider('{#age_min#}', 'filter_age_start', $age_min, 18, 50, 1);
        $this->data->scroll[] = $this->formkitSlider('{#age_max#}', 'filter_age_end', $age_max, 19, 80, 1);

        if(!isset($this->varcontent['distance'])){
            AeplayVariable::updateWithName($this->playid, 'distance' ,6213, $this->gid);
            $distance = 6213; // 10k KM in miles
        } else {
            $distance = $this->varcontent['distance'];
        }

        if(stristr($this->menuid,'distance_')){
            $dist = str_replace('distance_','',$this->menuid);
            AeplayVariable::updateWithName($this->playid, 'distance', $dist, $this->gid);
            $distance = $dist;
        }

        if(isset($this->menuid) AND $this->menuid == 'reset-matches') {
            $this->initMobileMatching();
            $this->mobilematchingobj->resetMatches();
        }

        $onclick1['action'] = 'submit-form-content';
        $onclick1['id'] = 'reset-matches';
        $onclick2['action'] = 'go-home';

        $options['style'] = 'general_button_style_red';
        $options['onclick'] = array($onclick1,$onclick2);

        $this->data->scroll[] = $this->formkitBox(array(
            'title' => '{#searching_for#}',
            'edit_type' => 'popup',
            'user_vars' => $this->varcontent,
            'edit_action_id' => $this->getConfigParam( 'detail_view' ),
            'variables' => array(
                'gender_preferences' => 'GENDER',
                'sexual_preferences' => 'SEXUAL ORIENTATION',
                'transgender_preferences' => 'TRANSGENDER',
                'filter_age_start' => 'AGE MIN',
                'filter_age_end' => 'AGE MAX',
            ),
        ));

        $this->data->scroll[] = $this->getText('{#reset_my_matches#}', $options);

        if ( $this->menuid == 'save-data' ) {

            if ( empty($this->error) ) {
                $label = '{#saved#}';
            } else {
                $label = '{#save#}';
            }
    
            $this->data->footer[] = $this->getTextbutton($label,array('id' => 'save-data', 'style' => 'general_button_style_red'));

        } else {
            $this->data->footer[] = $this->getTextbutton('{#save#}',array('id' => 'save-data', 'style' => 'general_button_style_red'));
        }
        
        return $this->data;
    }

    public function validateAndSave() {

        if ( $this->menuid != 'save-data' ) {
            return false;
        }

        $id_age_min = $this->getVariableId( 'filter_age_start' );
        $id_age_max = $this->getVariableId( 'filter_age_end' );

        $curr_age_min = $this->submitvariables[$id_age_min];
        $curr_age_max = $this->submitvariables[$id_age_max];

        if ( $curr_age_min > $curr_age_max ) {
            $this->error_messages[] = 'The selected min age should be less than the selected max age';
            $this->error = true;
        }

        if ( empty($this->error) ) {
            $this->saveVariables();
            $this->loadVariableContent(true);
        } else {
            // Display the error messages in the footer section
            $this->displayErrors();
        }

    }

    public function varsMaps() {
        return array(
            'gender' => array(
                'label' => 'Gender',
                'options' => array( 'Female', 'Male', 'Genderqueer', 'Intersex', 'Other', ),
                'type' => 'radio',
            ),
            'gender_preferences' => array(
                'label' => 'For what gender are you looking for?',
                'options' => array( 'Female', 'Male', 'Genderqueer', 'Intersex', 'Other', ),
                'type' => 'checkboxes',
            ),
            'sexual_orientaion' => array(
                'label' => 'Sexual orientation',
                'options' => array( 'Bisexual', 'Gay', 'Lesbian', 'Queer', 'Straight', 'Other', ),
                'type' => 'radio',
            ),
            'sexual_preferences' => array(
                'label' => 'What sexual orientation are you looking for?',
                'options' => array( 'Bisexual', 'Gay', 'Lesbian', 'Queer', 'Straight', 'Other', ),
                'type' => 'checkboxes',
            ),
            'is_transgender' => array(
                'label' => 'Transgender',
                'options' => array( 'Yes', 'No', 'No preference' ),
                'type' => 'radio',
            ),
            'transgender_preferences' => array(
                'label' => 'Transgender',
                'options' => array( 'Yes', 'No', 'No preference' ),
                'type' => 'radio',
            ),
            'education' => array(
                'label' => 'What is your level of education?',
                'options' => array( 'No answer', 'High school', 'Some college', 'Associates degree', 'Bachelors degree', 'Graduate degree', 'PhD / Post Doctoral', ),
                'type' => 'radio',
            ),
            'languages' => array(
                'label' => 'What languages do you speak?',
                'options' => array( 'Arabic', 'Chinese', 'Dutch', 'English', 'French', 'German', 'Hebrew', 'Hindi', 'Italian', 'Japanese', 'Korean', 'Norwegian', 'Portuguese', 'Russian', 'Spanish', 'Swedish', 'Tagalog', 'Urdu', 'Other', ),
                'type' => 'checkboxes',
            ),
            'living_data' => array(
                'label' => 'What do you do for a living?',
                'options' => array( 'No answer', 'Administrative / Secretarial', 'Architecture / Interior design', 'Artistic / Creative / Performance', 'Education / Teacher / Professor', 'Executive / Management', 'Fashion / Model / Beauty', 'Financial / Accounting / Real Estate', 'Labor / Construction', 'Law enforcement / Security / Military', 'Legal', 'Medical / Dental / Veterinary / Fitness', 'Nonprofit / Volunteer / Activist', 'Political / Govt / Civil Service / Military', 'Retail / Food services', 'Retired', 'Sales / Marketing', 'Self-Employed / Entrepreneur', 'Student', 'Technical / Science / Computers / Engineering', 'Travel / Hospitality / Transportation', 'Other profession' ),
                'type' => 'radio',
            ),
            'exercise_data' => array(
                'label' => 'How often do you exercise?',
                'options' => array( 'No answer', 'Never', 'Exercise 1-2 times per week', 'Exercise 3-4 times per week', 'Exercise 5 or more times per week', ),
                'type' => 'radio',
            ),
            'smoke_data' => array(
                'label' => 'Do you smoke?',
                'options' => array( 'No answer', 'No way', 'Occasionally', 'Daily', 'Cigar aficionado', 'Yes, but trying to quit', ),
                'type' => 'radio',
            ),
            'drinks_data' => array(
                'label' => 'How often do you drink?',
                'options' => array( 'No answer', 'Never', 'Social drinker', 'Moderately', 'Regularly', ),
                'type' => 'radio',
            ),
            'hobby_data' => array(
                'label' => 'Have a hobby? Pick some you like to do.',
                'options' => array( 'Alumni connections', 'Book club', 'Business networking', 'Camping', 'Coffee and conversation', 'Cooking', 'Dining out', 'Exploring new areas', 'Fishing / Hunting', 'Gardening / Landscaping', 'Hobbies and crafts', 'Museums and art', 'Music and concerts', 'Nightclubs / Dancing', 'Performing arts', 'Playing cards', 'Playing sports', 'Political interests', 'Religion / Spiritual', 'Shopping / Antiques', 'Travel / Sightseeing', 'Video games', 'Volunteering', 'Watching sports', 'Wine tasting', ),
                'type' => 'checkboxes',
            ),
            'sports_data' => array(
                'label' => 'What kinds of sports and exercise do you enjoy?',
                'options' => array( 'Aerobics', 'Auto racing / Motocross', 'Baseball', 'Basketball', 'Billiards / Pool', 'Bowling', 'Cycling', 'Dancing', 'Football', 'Golf', 'Hockey', 'Inline skating', 'Martial arts', 'Running', 'Skiing', 'Soccer', 'Swimming', 'Tennis / Racquet sports', 'Volleyball', 'Walking / Hiking', 'Weights / Machines', 'Yoga', 'Other types of exercise', ),
                'type' => 'checkboxes',
            ),
        );
    }

    public function getVariablesView() {

        if ( $this->menuid == 'save-preferences' ) {
            $this->savePreferences();
        }

        $query_vars = $this->sessionGet( 'query_vars' );

        if ( preg_match('~edit-vars~', $this->menuid) ) {
            $query_vars = str_replace('edit-vars-', '', $this->menuid);
            $this->sessionSet( 'query_vars', $query_vars );
        }

        if ( empty($query_vars) ) {
            $this->data->scroll[] = $this->getText( 'Error', array( 'text-align' => 'center', 'padding' => '20 20 20 20' ) );
            return $this->data;
        }


        $vars_data = explode('|', $query_vars);
        $map = $this->varsMaps();

        foreach ($vars_data as $var_key) {
            if ( isset($map[$var_key]) ) {
                $this->getUpdateBox( $map[$var_key], $var_key );
            }
        }

        $this->data->footer[] = $this->getTextbutton('{#save#}',array('style' => 'general_button_style_gray', 'id' => 'save-preferences'));

    }

    public function getUpdateBox( $data, $variable ) {

        $label = $data['label'];
        $type = $data['type'];
        $current_val = $this->getSavedVariable( $variable );

        $active_values = array( $current_val );

        if ( $type == 'checkboxes' ) {
            $active_values = json_decode( $current_val, true );
        }

        $items = $data['options'];

        $this->data->scroll[] = $this->getText($label, array(
            'text-align' => 'center',
            'padding' => '8 5 8 5',
            'background-color' => '#ebeaf1',
        ));
        
        foreach ($items as $item) {
            $left_col = $this->getText( $item, array( 'font-size' => '14' ) );

            $selectstate = array(
                'style' => 'gaybff-selected',
                'variable_value' => $item,
                'allow_unselect' => 1,
                'animation' => 'fade'
            );

            if ( !empty($current_val) AND in_array($item, $active_values) ) {
                $selectstate['active'] = '1';
            }

            $right_col = $this->getText('', array(
                'variable' => ( $type == 'radio' ? $variable : $variable . '_' . $item ),
                'variable_value' => $item,
                'style' => 'gaybff-unselected',
                'allow_unselect' => 1,
                'selected_state' => $selectstate,
            ));

            $this->data->scroll[] = $this->getRow(array(
                $left_col, $right_col
            ), array( 'padding' => '5 10 5 10', 'margin' => '4 0 4 0' ));
        }

    }

    public function savePreferences() {

        // Get the variables, which the user is currently updating
        $query_vars = $this->sessionGet( 'query_vars' );
        $vars_data = explode('|', $query_vars);
        $map = $this->varsMaps();

        $error = false;

        foreach ($vars_data as $variable) {

            if ( !isset($map[$variable]) ) {
                continue;
            }

            $data = $map[$variable];
            $type = $data['type'];
            $label = $data['label'];
            $error_text = 'Please select ' . strtolower( $label );
            
            if ( $type == 'checkboxes' ) {
                
                $savearray = array();
                $handler = $variable . '_';

                foreach($this->submitvariables as $key => $value){
                    if ( stristr($key, $handler) AND !empty($value) ) {
                        $id = str_replace( $handler, '', $key );
                        $savearray[$id] = $value;
                    }
                }

                if ( !empty($savearray) ) {
                    $this->saveVariable($variable, json_encode($savearray));
                } else {
                    $this->data->footer[] = $this->getText($error_text, array('style' => 'register-text-step-2'));
                    $error = true;
                }

            } else {
                $this->saveVariables();
                $this->loadVariableContent();

                $current_val = $this->getVariable( $variable );

                if ( empty($current_val) ) {
                    $this->data->footer[] = $this->getText($error_text, array('style' => 'register-text-step-2'));
                    $error = true;                
                }
            }

        }

        if ( !$error ) {
            $this->forcePopupClose();
            return true;
        }

    }

    public function forcePopupClose() {
        $cmd = new StdClass();
        $cmd->action = 'open-action';
        $cmd->action_config = $this->getConfigParam( 'return_action_id' );
        $this->data->onload[] = $cmd;
    }

}