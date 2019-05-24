<?php

class dittoMobileregister2SubController extends Mobileregister2Controller {

    public function dittoPhase1(){

        if ( $this->menuid == 'continue_to_3' ) {
            $this->dittoPhase2();
            return true;
        }

        $cache_name = 'location-asked' . $this->playid;

        $variables = $this->getPlayVariables();
        $token = ( isset($variables['fb_token']) ? $variables['fb_token'] : '' );
        $fb_data = ThirdpartyServices::getCompleteFBInfo( $token );
        $cache = Appcaching::getGlobalCache( $cache_name );

        if(!$cache AND !$this->getSavedVariable('lat')){
            
            $menu2 = new StdClass();
            $menu2->action = 'ask-location';
            $menu2->sync_open = 1;
            $this->data->onload[] = $menu2;
            Appcaching::setGlobalCache( $cache_name, true);

        } elseif($this->getSavedVariable('lat')){
            Appcaching::setGlobalCache( $cache_name, true);
        }

        $this->saveVariables();

        // This sets the default preferences as well as some additional Facebook Info
        $this->setExtraProfileData( $fb_data );
        $this->loadVariableContent(true);

        if ( isset($variables['name']) AND !empty($variables['name']) ) {
            $this->data->scroll[] = $this->getText( $variables['name'], array(
                'color' => '#ffffff',
                'text-align' => 'center',
                'font-size' => '25',
                'padding' => '15 10 10 10',
                'font-ios' => 'Lato-Bold',
                'font-android' => 'Lato-Bold'
            ));
        }

        // $this->closeLogin();
        $this->setProfilepic();

        if ( !$this->getSavedVariable('gender') ) {
            $this->setSex();
        }

        if ( !$this->getSavedVariable('age') ) {
            $this->setAge();
        }

        $this->setProfileComment();
        // $this->setTerms();

        $location = $this->getConfigParam('ask_for_location');
        $lat = $this->getSavedVariable('lat');
        $terms = $this->getSavedVariable('terms_accepted');
        $profilepic = $this->getSavedVariable('profilepic');
        $profilecomment = $this->getSavedVariable('profile_comment');
        $age = $this->getSavedVariable('age');
        $gender = $this->getSavedVariable('gender');

        if (!$lat AND $cache) {
            $this->geolocateMe();
            $error = true;
            $locationerror = true;
        }

        if($this->menuid == 'saver-2'){
            $error = false;

            if(!$profilepic){
                $this->data->footer[] = $this->getText('{#please_add_a_profile_pic#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

            if(!$profilecomment){
                $this->data->footer[] = $this->getText('{#please_add_a_profile_description#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

            if ( empty($age) ) {
                $this->data->footer[] = $this->getText('{#please_add_your_age#}',array('style' => 'register-text-step-2'));
                $error = true;
            } else if ( $age AND !is_numeric($age) ) {
                $this->data->footer[] = $this->getText('{#your_age_must_be_numeric#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

            if ( empty($gender) ) {
                $this->data->footer[] = $this->getText('{#please_select_your_gender#}',array('style' => 'register-text-step-2'));
                $error = true;
            }

            if($error == false){
                $this->data->scroll = array();
                $this->data->scroll[] = $this->getSpacer('100');
                $this->data->scroll[] = $this->getText('{#loading_matches#}...',array('style' => 'register-text-step-2'));
                $this->data->footer = array();
                $this->finishUp();

                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;

                return true;
            }
        }

        /* if there is location error, we will provide a locate button instead of this */
        if(!isset($locationerror)){
            $this->data->footer[] = $this->getTextbutton('{#continue#}',array('style' => 'general_button_style_footer','id' => 'saver-2'));
        }

        $this->data->scroll[] = $this->getSpacer(40);

        return true;
    }

    public function dittoPhase2(){

        $this->saveVariables();

        $terms = $this->getSavedVariable('approve_terms');
        $lat = $this->getSavedVariable('lat');

        $this->saveVariable('reg_phase',3);
        $lon = $this->getSavedVariable('lon');

        $buttonparams['onclick'] = new StdClass();
        $buttonparams['onclick']->action = 'ask-location';
        $buttonparams['onclick']->sync_open = 1;
        $buttonparams['onclick']->sync_upload = false;
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

    public function dittoPhasecomplete(){
        $this->finishUp();
        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->scroll[] = $this->getSpacer('100');
        $this->data->scroll[] = $this->getText('{#loading_matches#}',array('style' => 'register-text-step-2'));
        $this->data->onload[] = $complete;
    }

    public function setProfilePic() {

        if(isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']){
            $pic = $this->varcontent['fb_image'];
        } elseif(isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
            $pic = $this->varcontent['profilepic'];
        } else {
            $pic = 'small-filmstrip.png';
        }

        $name = 'profilepic';

        $params['imgwidth'] = '600';
        $params['imgheight'] = '600';
        $params['margin'] = '10 90 10 90';

        $params['imgcrop'] = 'yes';
        $params['crop'] = 'round';
        $params['defaultimage'] = 'small-filmstrip.png';

        $params['onclick'] = new StdClass();
        $params['onclick']->action = 'upload-image';
        $params['onclick']->max_dimensions = '600';
        $params['onclick']->variable = $this->getVariableId($name);
        $params['onclick']->action_config = $this->getVariableId($name);
        $params['onclick']->sync_upload = true;

        $params['variable'] = $this->getVariableId($name);
        $params['config'] = $this->getVariableId($name);
        $params['debug'] = 1;
        $params['fallback_image'] = 'selecting-image.png';
        $params['priority'] = 9;

        $this->data->scroll[] = $this->getImage($pic, $params);
    }

    public function setSex() {

        $sex = $this->getSavedVariable( 'gender' );

        $this->data->scroll[] = $this->getText('Gender', array( 'style' => 'registration-heading' ));

        switch ($this->menuid) {
            case 'man':
                $class_man = 'radiobutton_selected';
                $class_women = 'radiobutton';
                $this->saveVariable('gender','man');
                break;

            case 'woman':
                $class_man = 'radiobutton';
                $class_women = 'radiobutton_selected';
                $this->saveVariable('gender','woman');
                break;

            default:

                if ( $sex == 'man' ) {
                    $class_man = 'radiobutton_selected';
                    $class_women = 'radiobutton';
                } else if ( $sex == 'woman' ) {
                    $class_man = 'radiobutton';
                    $class_women = 'radiobutton_selected';
                } else {
                    $class_man = 'radiobutton_selected';
                    $class_women = 'radiobutton';
                }

                break;
        }

        $columns[] = $this->getTextbutton('{#man#}', array('id' => 'man', 'style' => $class_man, 'sync_upload' => false));
        $columns[] = $this->getText('', array('margin' => '0 15 0 15'));
        $columns[] = $this->getTextbutton('{#woman#}', array('id' => 'woman', 'style' => $class_women, 'sync_upload' => false));

        $this->data->scroll[] = $this->getRow($columns, array('style' => 'general_button_style'));
    }

    public function setAge() {
        $age_var_id = $this->getVariableId( 'age' );

        $this->data->scroll[] = $this->getText('Age', array( 'style' => 'registration-heading' ));
        $this->data->scroll[] = $this->getFieldtext('', array( 'style' => 'registration-input', 'input_type' => 'number', 'hint' => 'Enter your age', 'variable' => $age_var_id ));
    }

    public function setExtraProfileData( $fb_data = false ) {

        if ( !empty($fb_data) ) {
            if ( isset($fb_data->email) ) {
                $this->saveVariable( 'email', $fb_data->email );
            }

            if ( isset($fb_data->birthday) ) {
                $birth_stamp = strtotime( $fb_data->birthday );
                $age = $this->computeAge( $birth_stamp, time() );
                $this->saveVariable( 'age', $age );
            }

            if ( isset($fb_data->gender) ) {
                $gender = $fb_data->gender;
                $gender = ( $gender == 'male' ? 'man' : 'woman' );
                $this->saveVariable( 'gender', $gender );
            }
        }

        $this->saveVariable('date_preferences', 'acceptor');
    }

    public function setProfileComment() {

        if (isset($this->varcontent['profile_comment'])) {
            $commentcontent = $this->varcontent['profile_comment'];
        } else {
            $commentcontent = '';
        }

        $args = array(
            'variable' => $this->getVariableId('profile_comment'),
            'hint' => 'What\'s interesting about you...?',
            'style' => 'general_textarea',
            'submit_menu_id' => 'continue_to_3'
        );

        $this->data->scroll[] = $this->getFieldtextarea($commentcontent, $args);
    }

    public function computeAge($starttime, $endtime) {
        $age = date('Y', $endtime) - date('Y', $starttime);

        //if birthday didn't occur that last year, then decrement
        if ( date('z', $endtime) < date('z',$starttime) ) $age--;

        return $age;
    }

    public function finishUp(){

        if(!$this->getSavedVariable('gender')){
            $this->saveVariable('gender','man');
        }

        $this->saveVariable( 'date_phase', 'step-1' );

        $this->updateLocalRegVars();

        // "complete" the registration process
        $this->beforeFinishRegistration();

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

        $gender = $this->getSavedVariable('gender');

        if ( $gender == 'man' ) {
            $this->saveVariable( 'look_for_women', 1 );
            $this->saveVariable( 'look_for_men', 0 );
        } else if ( $gender == 'woman' ) {
            $this->saveVariable( 'look_for_men', 1 );
            $this->saveVariable( 'look_for_women', 0 );
        }

        $this->saveVariable('logged_in', 1);
        $this->saveVariable('notify', 1);
        $this->saveVariable('men', 1);
        $this->saveVariable('women', 1);
    }

}