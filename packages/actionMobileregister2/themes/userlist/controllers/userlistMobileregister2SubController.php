<?php


Yii::import('application.modules.aelogic.packages.actionMobilefeedbacktool.models.*');


class userlistMobileregister2SubController extends Mobileregister2Controller {

    /** @var MobileloginModel */
    public $loginmodel;
    public $errors;

    public $userlist;

    /* @var MobilefeedbacktoolModel */
    public $dataobj;

    public function initUserlist(){
        $this->dataobj = new MobilefeedbacktoolModel();
        $this->dataobj->factoryInit($this);
        $this->dataobj->gid = $this->gid;
        $this->dataobj->author_id = $this->playid;
        $this->dataobj->playid = $this->playid;
        $this->initKeyValueStorage();
        $this->userlist = $this->dataobj->initUserlist(true);
        $this->initLoginModel();
    }
    
    public function setHeader(){
        if($this->getConfigParam('actionimage1')){
            $image = $this->getConfigParam('actionimage1');
            $this->data->header[] = $this->getImage($image);
        }

        $this->data->scroll[] = $this->getSpacer('10');
    }
    public function userlistPhase1(){

        if($this->getSubmittedVariableByName('tempusername')){
            if(trim(strtolower($this->getSubmittedVariableByName('tempusername'))) == 'bank' AND
            trim(strtolower($this->getSubmittedVariableByName('temppassword'))) == 'fun'){
                $this->saveVariable('authenticated', true);
            } else {
                $this->data->footer[] = $this->getText('{#please_check_your_info#}',array('style' => 'register-text-step-error'));
            }
        }



        if(!$this->getSavedVariable('authenticated') AND $this->getConfigParam('require_username')){

            $this->setHeader();
            $this->data->scroll[] = $this->getHairline('#E9E9E9');
            $this->data->scroll[] = $this->getText('{#username#}',array('text-align' => 'center','margin' => '5 0 5 0'));
            $this->data->scroll[] = $this->getHairline('#E9E9E9');
            $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('tempusername'),array('font-ios' => 'Roboto','text-align' => 'center','variable' => 'tempusername','input_type' => 'name'));

            $this->data->scroll[] = $this->getHairline('#E9E9E9');
            $this->data->scroll[] = $this->getText('{#password#}',array('text-align' => 'center','margin' => '5 0 5 0'));
            $this->data->scroll[] = $this->getHairline('#E9E9E9');
            $this->data->scroll[] = $this->getFieldPassword('',array('font-ios' => 'Roboto','text-align' => 'center','variable' => 'temppassword','input_type' => 'name'));
            $this->data->scroll[] = $this->getHairline('#E9E9E9');

            $this->data->footer[] = $this->getHairline('#E9E9E9');
            $this->data->footer[] = $this->getTextbutton('{#login#}',array('style' => 'general_button_style_footer','id' => 'first_register','submit_menu_id' => 'saver'));

            return true;

        }


        $this->initUserlist();

        if($this->menuid == 'reg_continue'){

            // enable this for apple
            $firstname = strtolower(trim($this->getSubmittedVariableByName('firstname')));
            $lastname = strtolower(trim($this->getSubmittedVariableByName('lastname')));
            $department = $this->getSubmittedVariableByName('department');
            $fullname = ucfirst($firstname) .' ' .ucfirst($lastname);

/*            if(strtolower($fullname) == 'timo railo'){
                    $this->userlistPhase3();
                    return true;
            }*/

            if($this->validate()){
                $this->userlistPhase2();
                return true;
            } elseif($this->getSavedVariable('authenticated')) {
                $this->data->footer[] = $this->getText('{#please_check_your_info#}',array('style' => 'register-text-step-error'));
                $this->data->footer[] = $this->getSpacer(8);
            }
        }

        $this->setHeader();
        $this->setName();
        $this->setDepartments();

        //$this->data->header[] = $this->getText('{#please_register#}', array( 'margin' => '10 0 30 0', 'text-align' => 'center', 'font-size' => '26'));
        $this->data->footer[] = $this->getHairline('#E9E9E9');
        $this->data->footer[] = $this->getTextbutton('{#register#}',array('style' => 'general_button_style_footer','id' => 'reg_continue','submit_menu_id' => 'saver'));
        return true;
    }

    public function setName(){
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getText('{#first_name#}',array('text-align' => 'center','margin' => '5 0 5 0'));
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('firstname'),array('font-ios' => 'Roboto','text-align' => 'center','variable' => 'firstname','input_type' => 'name'));

        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getText('{#last_name#}',array('text-align' => 'center','margin' => '5 0 5 0'));
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('lastname'),array('font-ios' => 'Roboto','text-align' => 'center','variable' => 'lastname','input_type' => 'name'));
    }
    
    public function setDepartments(){
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getText('{#choose_your_department#}',array('text-align' => 'center','margin' => '5 0 5 0'));
        $this->data->scroll[] = $this->getHairline('#E9E9E9');

        if($this->userlist['departments']){
            $list = '';

            foreach($this->userlist['departments'] as $dept){
                $list .= $dept .';' .$dept .';';
            }

            $list = substr($list,0,-1);
        }

        if(isset($list)){
            $this->data->scroll[] = $this->getFieldlist($list,array('font-ios' => 'Roboto','variable' => 'department','value' => $this->getSubmittedVariableByName('department')));
        }
    }

    public function validate(){

        $fullname = $this->getAndSaveRealName();
        $department = $this->getSubmittedVariableByName('department') ? $this->getSubmittedVariableByName('department') : $this->getSavedVariable('department');
        $this->saveVariable('department',$department);

        if(isset($this->userlist['names'][$fullname]['department_name'])
            AND $this->userlist['names'][$fullname]['department_name'] == $department
            AND isset($this->userlist['names'][$fullname]['email'])
        ){

            return true;
        }

        if(isset($this->userlist['names'][$fullname]['department_name'])){
            $this->addToDebug('department_name:' .$this->userlist['names'][$fullname]['department_name']);
        } else {
            $this->addToDebug('department not found from userlist');
        }

        if(!isset($this->userlist['names'][$fullname]['email'])){
            $this->addToDebug('email not found from userlist');
        }

        $this->addToDebug('submitted department'.$department);

        false;


    }

    public function collectLocation()
    {

        if(!$this->getSavedVariable('lat') OR !$this->getSavedVariable('lon')){
            $loc = $this->getCollectLocation();

            if ($loc){
                $this->data->onload[] = $loc;
            }

            if($this->menuid == 'reg_continue_2'){
                $this->errors = true;
                $this->data->footer[] = $this->getText('{#please_enable_location_to_finish#}',array('style' => 'register-text-step-error'));
                $this->data->footer[] = $this->getSpacer(8);
            }

        }

    }

    public function userlistPhase2(){
        $this->data = new stdClass();

        $this->initUserlist();

        /* deep linking support */
        if(stristr($this->menuid,'reg_continue_3_code_')){
            $code = str_replace('reg_continue_3_code_','',$this->menuid);

            if($code == $this->getSavedVariable('register_code')){
                $this->userlistPhase3();
                return true;
            }
        }

        if($this->menuid == 'reg_continue_3' AND $this->getSubmittedVariableByName('code')){
            if($this->getSubmittedVariableByName('code') == $this->getSavedVariable('register_code')){
                $this->userlistPhase3();
                return true;
            } else {
                $this->data->footer[] = $this->getText('{#please_check_the_code#}',array('style' => 'register-text-step-error'));
            }
        }

        $this->setHeader();
        $fullname = $this->getAndSaveRealName();

        if(isset($this->userlist['names'][$fullname]['email'])){
            $email = $this->userlist['names'][$fullname]['email'];
        }

        if(isset($email) AND $this->menuid == 'reg_continue' OR $this->menuid == 'resend_code'){
            $this->saveVariable('reg_phase',2);

            $code = ucfirst(Helper::generateShortcode());
            $this->saveVariable('register_code', $code);
            $from = isset($this->appinfo->name) ? $this->appinfo->name : 'Appzio';

            $config = json_decode($this->mobilesettings->config_main,true);
            if(isset($config['app_url'])){
                $appurl = $config['app_url'];
            } else {
                $appurl = $from;
            }

            $link = $appurl .'://open=action_id?' .$this->action_id .'&menuid=reg_continue_3_code_' . $code;
            $this->getAppUrl($this->action_id,'reg_continue_3_code_' . $code);
            $link = '<a href="'.$link.'">' .$link .'</a>';

            $mail = new YiiMailMessage;

            $body = $this->localizationComponent->smartLocalize('{#register_for#} '.$from);
            $body .= "<br /><br />";
            $body .= $this->localizationComponent->smartLocalize('{#please_enter_the_following_code_in_the_app#}: ');
            $body .= "<br /><br />";
            $body .= $this->localizationComponent->smartLocalize('{#code#}: ') . $code;
            $body .= "<br /><br />";

            $body .= $this->localizationComponent->smartLocalize('{#or_click_this_link_on_your_device#}') .': ' .$link;
            $body .= "<br /><br />";

            $body .= $this->localizationComponent->smartLocalize('{#best#},');
            $body .= "<br />";
            $body .= $this->localizationComponent->smartLocalize('{#email_signature_message#}');

            $mail->setBody($body, 'text/html');
            
            $mail->addTo( $email );

            if($this->getConfigParam('sender_email')){
                $email = $this->getConfigParam('sender_email');
            } else {
                $email = 'unifun-activation@appzio.com';
            }

            $mail->from = array($email => $from);
            $mail->subject = $this->localizationComponent->smartLocalize($from .' {#activation#}');

            try {
                Yii::app()->mail->send($mail);
            } catch (Exception $e) {
                $this->data->scroll[] = $this->getText('{#there_might_have_been_a_problem_with_sending_the_email#}. {#if_you_dont_receive_it_please_retry#}',array('text-align' => 'center','margin' => '5 0 5 0'));
                Controller::sendAdminEmail('Email problem with registration',json_encode($e));
            }
        }

        $this->data->scroll[] = $this->getText('{#please_check_your_email_and#}',array('text-align' => 'center','margin' => '5 0 5 0'));
        $this->data->scroll[] = $this->getText('{#enter_your_code_below_to_activate#}',array('text-align' => 'center','margin' => '5 0 20 0'));
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getFieldtext('',array('font-ios' => 'Roboto','text-align' => 'center','variable' => 'code',
            'submit_menu_id' => 'reg_continue_3',
        ));
        $this->data->scroll[] = $this->getHairline('#E9E9E9');
        $this->data->scroll[] = $this->getSpacer(15);

        $this->data->scroll[] = $this->getText('{#if_you_didnt_receive_your_code_you_can_try_resending_it#}. {#note_that_the_code_is_case_sensitive#}',array('text-align' => 'center','margin' => '5 40 10 40','font-size' => 13));


        $this->data->footer[] = $this->getHairline('#E9E9E9');

        $btnrow[] = $this->getVerticalSpacer('4%');
        $btnrow[] = $this->getTextbutton('{#resend_the_code#}',array('style' => 'halfsize_button_style_footer','id' => 'resend_code','submit_menu_id' => 'resend_code'));
        $btnrow[] = $this->getVerticalSpacer('4%');
        $btnrow[] = $this->getTextbutton('{#activate_the_app#}',array('style' => 'halfsize_button_style_footer','id' => 'reg_continue_3','submit_menu_id' => 'saver'));
        $btnrow[] = $this->getVerticalSpacer('4%');

        $this->data->footer[] = $this->getRow($btnrow);

    }

    private function getAndSaveRealName(){

        if($this->getSubmittedVariableByName('firstname') AND $this->getSubmittedVariableByName('lastname')){
            $firstname = $this->getSubmittedVariableByName('firstname');
            $lastname = $this->getSubmittedVariableByName('lastname');
        } elseif($this->getSavedVariable('real_name')){
            if(stristr($this->getSavedVariable('real_name'),' ')){
                $arr = explode(' ',$this->getSavedVariable('real_name'));
                $firstname = $arr[0];
                $lastname = $arr[1];
            }
        } elseif($this->getSavedVariable('firstname') AND $this->getSavedVariable('lastname')){
            $firstname = $this->getSavedVariable('firstname');
            $lastname = $this->getSavedVariable('lastname');
        }

        if(!isset($firstname) OR !isset($lastname)){
            return '';
        }

        $firstname = strtolower($firstname);
        $lastname = strtolower($lastname);

        $fullname = ucwords($firstname) .' ' .ucwords($lastname);
        
        if(stristr($fullname,'-')){
            $change = explode('-',$fullname);
            $out = '';
            foreach($change as $part){
                $out .= ucfirst($part) .'-';
            }
            $fullname = substr($out,0,-1);
        }

        if($this->getSavedVariable('real_name') != $fullname){
            $this->saveVariable('real_name',$fullname);
        }

        return $fullname;

    }



    public function userlistPhase3(){
        $this->data = new stdClass();
        $this->saveVariable('reg_phase',3);
        $this->initUserlist();

        /* check whether the user already exists */

        if(isset($this->userlist['current_user']['department_id'])){
            $this->saveVariable('department_id',$this->userlist['current_user']['department_id']);
            $this->varcontent['department_id'] = $this->userlist['current_user']['department_id'];
        }

        $user = $this->dataobj->findUser($this->varcontent,true);

        if($user){
            $this->loginmodel->switchPlay($user,true);
            $this->finishUp();
            $this->purgeUserlistCache();
            $this->data->scroll[] = $this->getText('{#user_exists_logging_you_in#}',array('text-align' => 'center','margin' => '5 0 20 0','font-size' => 13));
            $this->data->onload[] = $this->getCompleteAction();
            return true;
        }

        $this->collectLocation();
        $this->setProfilePic();
        //$this->setProfileComment();

        if($this->menuid == 'reg_continue_4' AND empty($this->errors)){
            $this->data = new stdClass();
            $this->data->scroll[] = $this->getFullPageLoader();
            $this->saveVariables();
            $this->finishUp();
            $this->purgeUserlistCache();
            $this->data->onload[] = $this->getCompleteAction();
            return true;
        }

        $this->data->footer[] = $this->getHairline('#E9E9E9');
        $this->data->footer[] = $this->getTextbutton('{#finish#}',array('style' => 'general_button_style_footer','id' => 'reg_continue_4','submit_menu_id' => 'saver'));

    }

    public function purgeUserlistCache(){
            Yii::import('application.modules.aelogic.packages.actionMobilefeedbacktool.models.*');
            $this->dataobj = new MobilefeedbacktoolModel();
            $this->dataobj->factoryInit($this);
            $this->dataobj->gid = $this->gid;
            $this->dataobj->author_id = $this->playid;
        $this->dataobj->playid = $this->playid;

        $this->initKeyValueStorage();
            $this->userlist = $this->dataobj->initUserlist(true);
    }

    public function collectName(){
        if ($this->getConfigParam('collect_name',1)) {
            $realname = $this->getAndSaveRealName();

            if($this->menuid == 'reg_continue' AND !$realname) {
                $error = '{#please_input_your_real_name#}';
            } else {
                $error = false;
            }

            $this->data->scroll[] = $this->formkitField('real_name','{#name#}','{#input_your_name#}',false,$error);
        }
    }

    public function collectNickname(){
        if($this->menuid == 'reg_continue') {
            $error = $this->checkForError('screen_name', '{#screen_name_already_taken#} / {#not_valid#}');
        } else {
            $error = false;
        }

        $this->data->scroll[] = $this->formkitField('screen_name','{#nickname#}','{#input_your_nickname#}',false,$error);
    }

    public function collectGender(){
        if($this->menuid == 'reg_continue') {
            $error = $this->checkForError('gender', '{#please_choose_your_gender#}');
        } else {
            $error = false;
        }

        $listparams['variable'] = 'gender';
        $interests = array('man'=>'{#male#}','woman'=>'{#female#}');
        $this->data->scroll[] = $this->formkitRadiobuttons('{#gender#}',$interests,$listparams,$error);

    }

    public function collectInterests(){
        if($this->menuid == 'reg_continue') {
            $error = $this->checkForError('interests', '{#please_choose_at_least_one_interest#}');

            /* saving tags */
            foreach($this->submitvariables as $key=>$val){
                if(stristr($key,'temp_interests_')){
                    $id = str_replace('temp_interests_','',$key);
                    $savearray[$id] = $val;
                    if($val){
                        $error = false;
                    }
                }
            }
        } else {
            $error = false;
        }

        $listparams['variable'] = 'temp_interests';
        $interests = array('pandas'=>'{#pandas#}','tigers'=>'{#tigers#}','flowers' => '{#flowers#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}',$interests,$listparams,$error);
    }


    public function collectApproval(){
        $error = false;

        if($this->menuid == 'reg_continue') {
            if(!$this->getSubmittedVariableByName('terms_accepted')){
                $error = '{#please_approve_the_terms#}';
            }
        }

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getConfigParam('terms_popup');
        $onclick->open_popup = 1;

        $this->data->scroll[] = $this->formkitCheckbox('terms_accepted','{#approve_the_terms#}',array('onclick' => $onclick),$error);
    }

    public function saveInterests(){
        foreach($this->submitvariables as $key=>$val){
            if(stristr($key,'temp_interests_')){
                $id = str_replace('temp_interests_','',$key);
                $savearray[$id] = $val;
            }
        }

        if(isset($savearray)){
            $this->saveVariable('interests',json_encode($savearray));
        }
    }

    public function setProfilePic(){

            $this->data->scroll[] = $this->getText('{#please_add_a_profile_photo#}', array( 'style' => 'register-text-step-2'));

            if(isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']){
                $pic = $this->varcontent['fb_image'];
                $txt = '{#change_the_photo#}';
            } elseif(isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
                $pic = $this->varcontent['profilepic'];
                $txt = '{#change_the_photo#}';
            } else {
                $pic = 'filmstrip-placeholder.png';
                $txt = '{#add_a_photo#}';
            }

            //$this->data->scroll[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'),'imgwidth' => '600','imgheight' => '600','imgcrop'=>'yes'));
            $img[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'), 'crop' => 'round', 'width' => '150','text-align' => 'center','floating' => "1",'float' => 'center',
                'border-width' => '5', 'border-color' => '#ffffff','border-radius' => '75',
                'action' => 'upload-image',
                'sync_upload'=>true,
                'max_dimensions' => '900'
                ));
            $img[] = $this->getText(' ');

            $this->data->scroll[] = $this->getColumn($img,array('text-align' => 'center','height' => '150','margin' => '8 0 8 0','floating' => "1",'float' => 'center'));

            if($pic == 'small-filmstrip.png' AND isset($this->menuid) AND ($this->menuid == 5555 OR $this->menuid == 'sex-woman')){
                $this->data->scroll[] = $this->getText('{#uploading#} ...', array( 'style' => 'uploading_text'));
            }

            $this->data->scroll[] = $this->getTextbutton($txt, array(
                'variable' => $this->vars['profilepic'],
                'action' => 'upload-image',
                'sync_upload'=>true,
                'max_dimensions' => '900',
                'style' => 'general_button_style' ,
                'id' => $this->vars['profilepic']));

            if($this->menuid == 'reg_continue_4' AND $pic == 'filmstrip-placeholder.png'){
                //$this->errors = true;
                //$this->data->footer[] = $this->getText('{#please_upload_image#}',array('style' => 'register-text-step-error'));
                //$this->data->footer[] = $this->getSpacer(8);
            }

        }





    public function setProfileComment() {

        if($this->getSubmittedVariableByName('profile_comment')){
            $commentcontent = $this->getSubmittedVariableByName('profile_comment');
        } elseif(isset($this->varcontent['profile_comment'])) {
            $commentcontent = $this->varcontent['profile_comment'];
        } elseif($this->getSavedVariable('instagram_bio')) {
            $commentcontent = $this->getSavedVariable('instagram_bio');
        } else {
            $commentcontent = '';
        }


        if($this->getSavedVariable('role') == 'brand') {
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#company_description#} ({#required#})', 'style' => 'general_textarea'));
        } else {
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#profile_comment#} ({#required#})', 'style' => 'general_textarea'));
        }

        if($this->menuid == 'reg_continue_2' AND !$commentcontent){
            $this->errors = true;
            $this->data->footer[] = $this->getText('{#please_input_profile_comment#}',array('style' => 'register-text-step-error'));
            $this->data->footer[] = $this->getSpacer(8);
        }


    }


    public function finishUp(){

        $this->initUserlist();
        $this->dataobj->invalidateCache($this->gid,$this->playid);
        
        if(!$this->getSavedVariable('gender')){
            $this->saveVariable('gender','man');
        }

        $this->updateLocalRegVars();
        $this->beforeFinishRegistration();

        $this->saveVariable('logged_in',1);
        $this->saveVariable('look_for_men',1);
        $this->saveVariable('look_for_women',1);
        $this->saveVariable('men',1);
        $this->saveVariable('women',1);

        MobilefeedbacktoolModel::invalidateCache($this->gid,$this->playid);

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

        $gender = $this->getSavedVariable('gender');

        if ( $gender == 'man' ) {
            $this->saveVariable( 'men', 0 );
            $this->saveVariable( 'women', 1 );
        } else if ( $gender == 'woman' ) {
            $this->saveVariable( 'men', 1 );
            $this->saveVariable( 'women', 0 );
        }

        // $this->saveVariable('logged_in', 1);
        $this->saveVariable('notify', 1);
    }
}