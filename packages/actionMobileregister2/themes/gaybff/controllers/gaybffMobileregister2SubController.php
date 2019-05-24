<?php

class gaybffMobileregister2SubController extends Mobileregister2Controller {

    public $step_one_skipped = false;

    public function tab1(){
        $this->theme = ( isset($this->configobj->article_action_theme) ? $this->configobj->article_action_theme : '' );
        $this->data = new StdClass();
        $this->phase = $this->getSavedVariable('reg_phase') ? $this->getSavedVariable('reg_phase') : 1;

        if($this->menuid == 'save-variables'){
            $this->saveVariables();
        }

        // Handle Back button
        if ( preg_match('~back-~', $this->menuid) ) {
            $this->phase = str_replace('back-', '', $this->menuid);
        }

        if($this->fblogin){
            $this->social_authentication = true;
        }

        $function = $this->theme . 'Phase' . $this->phase;

        if (method_exists($this,$function)) {
            $this->$function();
        } elseif($this->phase == 'complete') {
            $this->generalPhaseComplete();
        }

        return $this->data;
    }

	public function gaybffPhase1(){

        // User already Signed In with Facebook, so go to Step 2 instead
        if ( isset($this->varcontent['fb_token']) AND !empty($this->varcontent['fb_token']) ) {
            $this->saveVariable('reg_phase', 2);
            $this->step_one_skipped = true;
            $this->gaybffPhase2();
            return true;
        }

        if ( $this->getConfigParam( 'actionimage1' ) ) {
            $image_file = $this->getConfigParam( 'actionimage1' );
        } elseif ( $this->getImageFileName('reg-logo.png') ) {
            $image_file = 'reg-logo.png';
        }

        if(isset($image_file)){
            $this->data->scroll[] = $this->getImage( $image_file );
        }

        if($this->getSavedVariable('password') AND $this->menuid != 'mobilereg_do_registration'){

            if($this->menuid == 'create-new-user'){
                Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
                $loginmodel = new MobileloginModel();
                $loginmodel->userid = $this->userid;
                $loginmodel->playid = $this->playid;
                $loginmodel->gid = $this->gid;
                $play = $loginmodel->newPlay();
                $this->playid = $play;

                $this->data->scroll[] = $this->getText('{#creating_new_account#}', array( 'style' => 'register-text-step-2'));

                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;
                return true;
            } else {
                $this->data->scroll[] = $this->getSpacer('15');
                $this->data->scroll[] = $this->getText('{#are_you_sure#}', array( 'style' => 'register-text-step-2'));
                $this->data->scroll[] = $this->getBackButton();

                $this->data->scroll[] = $this->getSpacer('15');
                $buttonparams2 = new StdClass();
                $buttonparams2->action = 'submit-form-content';
                $buttonparams2->id = 'create-new-user';
                $this->data->footer[] = $this->getText('{#create_a_new_account#}',array('style' => 'general_button_style_footer','onclick' => $buttonparams2));
                return true;
            }
        }

        $this->saveVariable('reg_phase',1);
        $this->data->scroll[] = $this->getSpacer('15');

        if($this->fblogin === false) {
            if ($this->getConfigParam('login_branch')) {
                $this->data->scroll[] = $this->getBackButton();
            }
        }

        $this->data->scroll[] = $this->getSpacer('10');
        $regfields = $this->setRegFields();

        if($regfields === true){
            $this->saveRegData();
            $this->gaybffPhase2();
            return true;
        } else {
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getTextbutton('{#register#}',array('style' => 'general_button_style_footer','id' => 'mobilereg_do_registration','submit_menu_id' => 'saver'));
        }

        return true;
    }

    public function gaybffPhase2(){

        if( $this->menuid == 'continue_to_3' ){
            $this->gaybffPhaseLocate();
            return true;
        }

        $cache = Appcaching::getGlobalCache('location-asked'.$this->playid);

        if(!$cache){
            $buttonparams = new StdClass();
            $buttonparams->action = 'submit-form-content';
            $buttonparams->id = 'save-variables';
            $this->data->onload[] = $buttonparams;

            $menu2 = new StdClass();
            $menu2->action = 'ask-location';
            $this->data->onload[] = $menu2;
            Appcaching::setGlobalCache('location-asked'.$this->playid,true);
        }

        // $this->data->footer[] = $this->getText($this->getSavedVariable('lat'));

        $this->closeLogin();
        $this->setProfilepic();
        $this->setProfileComment();
        $this->setAge();
        $this->setTerms();

        $location = $this->getConfigParam('ask_for_location');
        $lat = $this->getSavedVariable('lat');

        $profilepic = $this->getSavedVariable('profilepic');
        $profilecomment = $this->getSubmittedVariableByName('profile_comment');
        $terms = $this->getSubmittedVariableByName('terms_accepted');
        $age = $this->getSubmittedVariableByName( 'age' ) ? $this->getSubmittedVariableByName( 'age' ) : $this->getSavedVariable('age');

        $this->data->footer[] = $this->getHairline('#ffffff');

        if(!$lat AND $cache){
            $this->geolocateMe();
            $error = true;
            $locationerror = true;
        }

        if ( $this->menuid == 'saver-2' ){
            $error = false;

            if ( !$profilepic ) {
                $this->data->footer[] = $this->getText('{#please_add_a_profile_pic#}',array('style' => 'register-text-step-2'));
                $error = true;
            }
            
            if ( !$profilecomment ) {
                $this->data->footer[] = $this->getText('{#please_enter_something_about_you#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

            if ( empty($age) ) {
                $this->data->footer[] = $this->getText('{#please_enter_your_age#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

            if ( $age ) {
                if ( $age < 18 ) {
                    $this->data->footer[] = $this->getText('{#you_are_not_old_enought#}',array('style' => 'register-text-step-2'));
                    $error = true;
                } else if ( $age > 110 ) {
                    $this->data->footer[] = $this->getText('{#please_enter_your_real_age#}',array('style' => 'register-text-step-2'));
                    $error = true;
                }
            }

            if ( !$terms ) {
                $this->data->footer[] = $this->getText('{#please_accept_the_terms#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

            if ( empty($error) ) {

                if ( !$this->step_one_skipped ) {
                    $this->saveVariables();
                    $this->loadVariableContent(true);
                }

                $this->data->scroll = array();
                $this->data->footer = array();

                $this->gaybffPhase3();
                return true;
            }
        }

        /* if there is location error, we will provide a locate button instead of this */
        if ( !isset($locationerror) ) {
            $this->data->footer[] = $this->getTextbutton('{#continue#}',array('style' => 'general_button_style_footer','id' => 'saver-2'));
        }

        $this->data->scroll[] = $this->getSpacer(40);
        Appcaching::setGlobalCache( $this->playid .'-' .'registration',true);
        return true;
    }

    public function gaybffPhase3() {
        $variable = 'gender';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-3', 'Please select your gender', 'gaybffPhase4', $variable ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 3);

        $image_file = $this->getConfigParam( 'actionimage2' );
        $this->copyAssetWithoutProcessing( $image_file );
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('Gender', array( 'style' => 'gaybff-title' ));

        $options = array(
            'Female', 'Male', 'Genderqueer', 'Intersex', 'Other',
        );

        $this->getBffHeading( '{#please_select_your_gender#}' );

        $this->getControlButtons( $options, $variable, $current_val );

        $this->getPreferencesNavigation( 'saver-3' );
    }

    public function gaybffPhase4() {
        $variable = 'sexual_orientaion';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-4', 'Please choose your sexual orientation', 'gaybffPhase5', $variable ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 4);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('Sexual orientation', array( 'style' => 'gaybff-title' ));

        $options = array(
            'Bisexual', 'Gay', 'Lesbian', 'Queer', 'Straight', 'Other',
        );

        $this->getBffHeading( '{#please_select_your_sexual_orientation#}.' );

        $this->getControlButtons( $options, $variable, $current_val );

        $this->getPreferencesNavigation( 'saver-4', 'back-3' );
    }

    public function gaybffPhase5() {
        $variable = 'is_transgender';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-5', 'Please choose an answer', 'gaybffPhase6', $variable ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 5);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('Do you identify as transgender?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'Yes', 'No',
        );

        $this->getBffHeading( '{#please_select_an_answer#}.' );

        $this->getControlButtons( $options, $variable, $current_val );

        $this->getPreferencesNavigation( 'saver-5', 'back-4' );
    }

    public function gaybffPhase6() {
        $variable = 'transgender_preferences';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-6', 'Please choose an answer', 'gaybffPhase7', $variable ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 6);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('Should your GayBFF be transgender?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'Yes', 'No', 'No preference'
        );

        $this->getBffHeading( '{#please_select_an_answer#}.' );

        $this->getControlButtons( $options, $variable, $current_val );

        $this->getPreferencesNavigation( 'saver-6', 'back-5' );
    }

    public function gaybffPhase7() {
        $variable = 'gender_preferences';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-7', 'Please select what your GayBFF should be', 'gaybffPhase8', $variable, 'save-checkboxes' ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 7);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('My GayBFF should be?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'Female', 'Male', 'Genderqueer', 'Intersex', 'Other'
        );

        $this->getBffHeading( '{#please_select_all_that_apply#}' );

        $this->getControlButtons( $options, $variable, $current_val, 'checkboxes' );

        $this->getPreferencesNavigation( 'saver-7', 'back-6' );
    }

    public function gaybffPhase8() {
        $variable = 'sexual_preferences';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-8', 'Please select what your GayBFF should be', 'gaybffPhase9', $variable, 'save-checkboxes' ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 8);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('My GayBFF should be?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'Bisexual', 'Gay', 'Lesbian', 'Queer', 'Straight', 'Other',
        );

        $this->getBffHeading( '{#please_select_all_that_apply#}' );

        $this->getControlButtons( $options, $variable, $current_val, 'checkboxes' );

        $this->getPreferencesNavigation( 'saver-8', 'back-7' );
    }

    public function gaybffPhase9() {

        if ( $this->menuid == 'saver-9' ) {

            $error = false;

            $this->saveVariables();
            $this->loadVariableContent();

            $age_min = $this->getVariable( 'filter_age_start' );
            $age_max = $this->getVariable( 'filter_age_end' );

            if ( $age_min > $age_max ) {
                $error_text = 'The selected min age should be less than the selected max age';
                $this->data->footer[] = $this->getText($error_text, array('style' => 'register-text-step-2'));
                $error = true;                
            }

            if( empty($error) ){
                $this->gaybffPhase10();
                return true;
            }
        }

        $age_min = ( $this->getVariable( 'filter_age_start' ) ? $this->getVariable( 'filter_age_start' ) : 18 );
        $age_max = ( $this->getVariable( 'filter_age_end' ) ? $this->getVariable( 'filter_age_end' ) : 30 );

        $this->saveVariable('reg_phase', 9);

        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('My GayBFF should be?', array( 'style' => 'gaybff-title' ));

        $this->data->scroll[] = $this->formkitSlider('{#age_min#}', 'filter_age_start', $age_min, 18, 50, 1);
        $this->data->scroll[] = $this->formkitSlider('{#age_max#}', 'filter_age_end', $age_max, 19, 80, 1);

        $this->getPreferencesNavigation( 'saver-9', 'back-8' );
    }

    public function gaybffPhase10() {
        $variable = 'education';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-10', 'Please select your education', 'gaybffPhase11', $variable ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 10);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('What is your level of education?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'No answer', 'High school', 'Some college', 'Associates degree', 'Bachelors degree', 'Graduate degree', 'PhD / Post Doctoral',
        );

        $this->getBffHeading( '{#please_select_one#}' );

        $this->getControlButtons( $options, $variable, $current_val );

        $this->getPreferencesNavigation( 'saver-10', 'back-9' );
    }

    public function gaybffPhase11() {
        $variable = 'languages';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-11', 'Please select at least one language', 'gaybffPhase12', $variable, 'save-checkboxes' ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 11);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('What languages do you speak?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'Arabic', 'Chinese', 'Dutch', 'English', 'French', 'German', 'Hebrew', 'Hindi', 'Italian', 'Japanese', 'Korean', 'Norwegian', 'Portuguese', 'Russian', 'Spanish', 'Swedish', 'Tagalog', 'Urdu', 'Other',
        );

        $this->getBffHeading( '{#please_select_all_that_apply#}' );

        $this->getControlButtons( $options, $variable, $current_val, 'checkboxes' );

        $this->getPreferencesNavigation( 'saver-11', 'back-10' );
    }

    public function gaybffPhase12() {
        $variable = 'exercise_data';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-12', 'Please select how often do you exercise', 'gaybffPhase13', $variable ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 12);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('How often do you exercise?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'No answer', 'Never', 'Exercise 1-2 times per week', 'Exercise 3-4 times per week', 'Exercise 5 or more times per week',
        );

        $this->getBffHeading( '{#please_select_one#}' );

        $this->getControlButtons( $options, $variable, $current_val );

        $this->getPreferencesNavigation( 'saver-12', 'back-11' );
    }

    public function gaybffPhase13() {
        $variable = 'smoke_data';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-13', 'Please answer do you smoke', 'gaybffPhase14', $variable ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 13);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('Do you smoke?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'No answer', 'No way', 'Occasionally', 'Daily', 'Cigar aficionado', 'Yes, but trying to quit'
        );

        $this->getBffHeading( '{#please_select_one#}' );

        $this->getControlButtons( $options, $variable, $current_val );

        $this->getPreferencesNavigation( 'saver-13', 'back-12' );
    }

    public function gaybffPhase14() {
        $variable = 'drinks_data';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-14', 'Please answer how often do you drink', 'gaybffPhase15', $variable ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 14);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('How often do you drink?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'No answer', 'Never', 'Social drinker', 'Moderately', 'Regularly',
        );

        $this->getBffHeading( '{#please_select_one#}' );

        $this->getControlButtons( $options, $variable, $current_val );

        $this->getPreferencesNavigation( 'saver-14', 'back-13' );
    }

    public function gaybffPhase15() {
        $variable = 'living_data';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-15', 'Please select what do you do for a living', 'gaybffPhase16', $variable ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 15);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('What do you do for a living?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'No answer', 'Administrative / Secretarial', 'Architecture / Interior design', 'Artistic / Creative / Performance', 'Education / Teacher / Professor', 'Executive / Management', 'Fashion / Model / Beauty', 'Financial / Accounting / Real Estate', 'Labor / Construction', 'Law enforcement / Security / Military', 'Legal', 'Medical / Dental / Veterinary / Fitness', 'Nonprofit / Volunteer / Activist', 'Political / Govt / Civil Service / Military', 'Retail / Food services', 'Retired', 'Sales / Marketing', 'Self-Employed / Entrepreneur', 'Student', 'Technical / Science / Computers / Engineering', 'Travel / Hospitality / Transportation', 'Other profession'
        );

        $this->getBffHeading( '{#please_select_one#}' );

        $this->getControlButtons( $options, $variable, $current_val );

        $this->getPreferencesNavigation( 'saver-15', 'back-14' );
    }

    public function gaybffPhase16() {
        $variable = 'sports_data';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-16', 'Please select at least one', 'gaybffPhase17', $variable, 'save-checkboxes' ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 16);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('What kinds of sports and exercise do you enjoy?', array( 'style' => 'gaybff-title' ));

        $options = array(
            'Aerobics', 'Auto racing / Motocross', 'Baseball', 'Basketball', 'Billiards / Pool', 'Bowling', 'Cycling', 'Dancing', 'Football', 'Golf', 'Hockey', 'Inline skating', 'Martial arts', 'Running', 'Skiing', 'Soccer', 'Swimming', 'Tennis / Racquet sports', 'Volleyball', 'Walking / Hiking', 'Weights / Machines', 'Yoga', 'Other types of exercise'
        );

        $this->getBffHeading( '{#please_select_all_that_apply#}' );

        $this->getControlButtons( $options, $variable, $current_val, 'checkboxes' );

        $this->getPreferencesNavigation( 'saver-16', 'back-15' );
    }

    public function gaybffPhase17() {
        $variable = 'hobby_data';
        $current_val = $this->getVariable( $variable );

        if ( $this->handleNextStep( 'saver-17', 'Please select at least one', 'gaybffPhase18', $variable, 'save-checkboxes' ) ) {
            return true;
        }

        $this->saveVariable('reg_phase', 17);
        $this->configureBackground( 'actionimage2' );

        $this->data->scroll[] = $this->getText('Have a hobby? Pick some you like to do.', array( 'style' => 'gaybff-title' ));

        $options = array(
            'Alumni connections', 'Book club', 'Business networking', 'Camping', 'Coffee and conversation', 'Cooking', 'Dining out', 'Exploring new areas', 'Fishing / Hunting', 'Gardening / Landscaping', 'Hobbies and crafts', 'Museums and art', 'Music and concerts', 'Nightclubs / Dancing', 'Performing arts', 'Playing cards', 'Playing sports', 'Political interests', 'Religion / Spiritual', 'Shopping / Antiques', 'Travel / Sightseeing', 'Video games', 'Volunteering', 'Watching sports', 'Wine tasting'
        );

        $this->getBffHeading( '{#please_select_all_that_apply#}' );

        $this->getControlButtons( $options, $variable, $current_val, 'checkboxes' );

        $this->getPreferencesNavigation( 'saver-17', 'back-16', true );
    }

    public function gaybffPhase18() {
        $this->data->scroll = array();
        $this->data->scroll[] = $this->getSpacer('100');
        $this->data->scroll[] = $this->getText('{#loading_matches#}...',array('style' => 'register-text-step-2'));
        $this->data->footer = array();
        $this->finishUp();
        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;    
    }

    public function getPreferencesNavigation( $curr_btn, $prev_btn = false, $is_last = false ) {

        $back_button =  $this->getText('', array('style' => 'gaybff-btn-left'));

        if ( $prev_btn ) {
            $back_button = $this->getTextbutton('‹ {#back#}', array('id' => $prev_btn, 'viewport' => 'top', 'style' => 'gaybff-btn-left'));
        }

        $continue_text = ( $is_last ? '{#finish_registration#}' : '{#next#}' );

        $col_left = $this->getColumn(array(
            $back_button,
        ), array( 'style' => 'gaybff-reg-footer-cell' ));

        $col_right = $this->getColumn(array(
            $this->getTextbutton($continue_text . ' ›',array('id' => $curr_btn, 'viewport' => 'top', 'style' => 'gaybff-btn-right')),
        ), array( 'style' => 'gaybff-reg-footer-cell' ));

        $this->data->footer[] = $this->getHairline( '#d7d6dc' );
        $this->data->footer[] = $this->getRow(array(
            $col_left, $col_right
        ), array( 'style' => 'gaybff-reg-footer' ));
    }

    public function gaybffPhaseLocate(){

        $this->saveVariables();

        $terms = $this->getSavedVariable('approve_terms');
        $lat = $this->getSavedVariable('lat');

        $this->saveVariable('reg_phase',3);
        $lon = $this->getSavedVariable('lon');

        $btnaction1 = new StdClass();
        $btnaction1->action = 'submit-form-content';
        $btnaction1->id = 'save-variables';

        $btnaction2 = new StdClass();
        $btnaction2->action = 'ask-location';
        $btnaction2->sync_upload = false;

        $buttonparams['onclick'] = array($btnaction1,$btnaction2);

        $buttonparams['style'] = 'general_button_style_footer';
        $buttonparams['id'] = 'dolocate';
        $this->data->scroll[] = $this->getSpacer(200);

        if(!$lat){
            if(isset($this->menuid) AND $this->menuid == 'dolocate'){
                $this->data->scroll[] = $this->getText('Something went wrong with the location.', array( 'style' => 'register-text-step-2'));
            } else {
                $this->data->scroll[] = $this->getText('Registration requires your location information. Otherwise the app won\'t work.', array( 'style' => 'register-text-step-2'));
            }
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getText('Locate me now to continue', $buttonparams);
        } else {
            /* we do this only if the matching action is present within the app */

            $this->data->scroll[] = $this->getText('How wonderful, all required information is now set up. Please be respectful.', array( 'style' => 'register-text-step-2'));
            $this->generalComplete();
        }
    }

    public function handleNextStep( $menu_id, $error_text, $function_name, $variable, $save_type = false ) {

        if ( $this->menuid != $menu_id ) {
            return false;
        }

        $error = false;

        if ( $save_type == 'save-checkboxes' ) {
            
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

        if ( empty($error) ) {
            $this->{$function_name}();
            return true;
        }
    }

    public function getBffHeading( $heading ) {
        $this->data->scroll[] = $this->getSpacer( 10 );
        $this->data->scroll[] = $this->getText($heading, array( 'style' => 'gaybff-register-subheading' ));
        $this->data->scroll[] = $this->getHairline('#d0d0d5', array( 'margin' => '5 10 10 10' ));
    }

    public function getControlButtons( $items, $variable, $current_val, $type = 'radio' ) {

        if ( empty($items) ) {
            return false;
        }

        $active_values = array( $current_val );

        if ( $type == 'checkboxes' ) {
            $active_values = json_decode( $current_val, true );
        }
        
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

    public function finishUp(){

        if(!$this->getSavedVariable('gender')){
            $this->saveVariable('gender','man');
        }

        $this->updateLocalRegVars();

        $this->beforeFinishRegistration();

        if ( !$this->getConfigParam('require_match_entry') ) {
            return false;
        }

        $this->initMobileMatching();
        $this->mobilematchingobj->turnUserToItem(false,__FILE__);

        Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
        MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);
    }

    public function updateLocalRegVars() {

        // If user already completed the registration process
        if ( $this->getSavedVariable( 'reg_phase' ) == 'complete' ) {
            return false;
        }

        $this->loadVariableContent( true );

        /*
        $gender = $this->getSavedVariable('gender');

        if ( $gender == 'man' ) {
            $this->saveVariable( 'men', 0 );
            $this->saveVariable( 'look_for_men', 0 );
            $this->saveVariable( 'women', 1 );
            $this->saveVariable( 'look_for_women', 1 );
        } else if ( $gender == 'woman' ) {
            $this->saveVariable( 'men', 1 );
            $this->saveVariable( 'look_for_men', 1 );
            $this->saveVariable( 'women', 0 );
            $this->saveVariable( 'look_for_women', 0 );
        }
        */

        // $this->saveVariable('logged_in', 1);
        $this->saveVariable('notify', 1);
    }

    public function setRegFields(){
        $error = false;
        $error2 = false;
        $error3 = false;
        $error4 = false;
        $error5 = false;
        $error6 = false;

        if ($this->fblogin == false AND $this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getFacebookSignInButton('fb-login');

            if ($this->getConfigParam('collect_name',1)) {
                $realname = $this->getVariable('real_name');
                $name = $this->getVariable('name');

                if ($realname AND !$name) {
                    $this->saveVariable('name',$realname);
                }

                $error = $this->checkForError('real_name','{#please_input_first_and_last_name#}');
                $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png', $this->vars['real_name'], '{#nickname#}', $error, 'text', false, 'name');
            }
        } elseif ($this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getText("{#you_are_connected_with_facebook#}",array('style' => 'register-text-step-2'));
        } else {
            if ($this->getConfigParam('collect_name',1)) {
                $realname = $this->getVariable('real_name');
                $name = $this->getVariable('name');

                if ($realname AND !$name) {
                    $this->saveVariable('name',$realname);
                }

                $error2 = $this->checkForError('real_name','{#please_input_your_real_name#}');
                $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png', $this->vars['real_name'], '{#nickname#}', $error2, 'text', false, 'name');
            }
        }

        // $this->data->scroll[] = $this->getFieldWithIcon('icon-surname.png',$this->vars['surname'],'Surname',false,'text');

        if ($this->getConfigParam('show_email',1)) {
            $error3 = $this->checkForError('email','{#input_valid_email#}','{#email_already_exists#}');
            $this->data->scroll[] = $this->getFieldWithIcon('icon_email.png',$this->getVariableId('email'),'{#email#}',$error3);
        }

        if ($this->getConfigParam('collect_phone')) {
            $error4 = $this->checkForError('phone','{#please_input_a_valid_phone#}');
            $this->data->scroll[] = $this->getFieldWithIcon('phone-icon-register.png',$this->vars['phone'],'{#phone#} ({#with_country_code#}',$error4);
        }

        if ( $this->getConfigParam('collect_address') ) {
            $this->data->scroll[] = $this->getFieldWithIcon('icon-address.png',$this->vars['address'],'{#address#}',false,'text');
        }

        if ($this->getConfigParam('collect_password')) {
            $error5 = $this->checkForError('password_validity','{#at_least#} 4 {#characters#}');
            $error6 = $this->checkForError('password_match','{#passwords_do_not_match#}');
            $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->fields['password1'],'{#password#}',$error5,'password');
            $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->fields['password2'],'{#password_again#}',$error6,'password','mobilereg_do_registration');
        }

        $this->data->scroll[] = $this->getSpacer('5');

        if (!$error AND !$error2 AND !$error3 AND !$error4 AND !$error5 AND !$error6 AND $this->menuid == 'mobilereg_do_registration') {
            $this->saveVariable('reg_phase',2);
            unset($this->data->scroll);
            $this->data->scroll = array();
            // Need to investigate why ..
            unset($this->data->footer);
            return true;
        }

        return false;
    }

    public function setProfilePic(){

        if ( !$this->getConfigParam('require_photo') ) {
            return false;
        }

        $this->data->scroll[] = $this->getSpacer( 10 );
        $img_var = 'profilepic';

        if(isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']){
            $pic = $this->varcontent['fb_image'];
            $txt = '{#change_photo#}';
        } elseif(isset($this->varcontent[$img_var]) AND $this->varcontent[$img_var]) {
            $pic = $this->varcontent[$img_var];
            $txt = '{#change_photo#}';
        } else {
            $pic = 'small-filmstrip.png';
            $txt = '{#add_a_photo#}';
        }

        $image_upload = new StdClass();
        $image_upload->action = 'upload-image';
        $image_upload->max_dimensions = '900';
        $image_upload->variable = $this->getVariableId( $img_var );
        $image_upload->action_config = $this->getVariableId( $img_var );
        $image_upload->sync_upload = true;

        $img[] = $this->getImage($pic, array(
            'variable' => $this->getVariableId($img_var),
            'crop' => 'round',
            'width' => '200',
            'priority' => 9,
            'text-align' => 'center',
            'border-width' => '5',
            'border-color' => '#ffffff',
            'border-radius' => '100',
            'onclick' => $image_upload
        ));

        $this->data->scroll[] = $this->getColumn($img, array(
            'text-align' => 'center',
            'height' => '200',
            'margin' => '8 0 8 0'
        ));

        if($pic == 'small-filmstrip.png' AND isset($this->menuid) AND ($this->menuid == 5555 OR $this->menuid == 'sex-woman')){
            $this->data->scroll[] = $this->getText('{#uploading#} ...', array( 'style' => 'uploading_text'));
        }

        $this->data->scroll[] = $this->getTextbutton($txt, array(
            'variable' => $this->vars[$img_var],
            'action' => 'upload-image',
            'sync_upload'=>true,
            'max_dimensions' => '900',
            'style' => 'general_button_style_black' ,
            'id' => $this->vars[$img_var]
        ));

    }

    public function setProfileComment() {
        if ( !$this->getConfigParam('require_comment') ) {
            return false;
        }

        $commentcontent = '';

        if (isset($this->varcontent['profile_comment'])) {
            $commentcontent = $this->varcontent['profile_comment'];
        }        

        $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#about_yourself#}', 'style' => 'general_textarea'));
    }

    public function getBackButton() {

        $data = $this->getRow(array(
            $this->getTextbutton('‹ {#back_to_login#}', array(
                'style' => 'register-text-back-button',
                'id' => 'back',
                'action' => 'open-branch',
                'config' => $this->getConfigParam('login_branch'),
            )),
        ), array(
            'text-align' => 'center'
        ));

        return $data;
    }
    
}