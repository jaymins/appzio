<?php

class gaybffMobileuserprofileSubController extends MobileuserprofileController {

    public $user_vars;
    public $edit_type = false;
    public $edit_type_about = false;

    public function showProfileView(){

        /* determine who's profile should we show */
        $this->initMobileMatching();
        $this->mobilematchingobj->initMatching($this->profileid);
        $this->doReporting();

        $this->rewriteActionConfigField('background_color', '#F8F8FF');

        $vars = AeplayVariable::getArrayOfPlayvariables($this->profileid);
        $this->user_vars = $vars;

        $this->data->scroll[] = $this->getImageScroll($vars);

        $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );

        $txt = $this->getFirstName($vars);
        $city = isset($vars['city']) ? ThirdpartyServices::translateString( $vars['city'], 'en', $tr_lang ) : '{#hidden_city#}';
        $country = isset($vars['country']) ? ThirdpartyServices::translateString( $vars['country'], 'en', $tr_lang ) : '{#hidden_country#}';

        $txt2 = $city;
        if ( $country ) {
            $txt2 .= ', ' . $country;
        }

        $user[] = $this->getText($txt,array('height' => '30', 'margin' => '15 20 0 20','font-size' => '22'));
        $user[] = $this->getText($txt2,array('height' => '30', 'margin' => '21 23 0 20', 'font-size' => '16', 'floating' => 1, 'float' => 'right'));

        $this->data->scroll[] = $this->getRow($user, array( 'vertical-align' => 'middle' ));

        /*
        if(!isset($vars['real_name'])){
            $this->data->scroll[] = $this->getText('{#info_missing#}');
            return true;
        }
        */

        $ref_name = '';
        $cache_name = 'current-user-branch-' . $this->playid;
        $cached_value = Appcaching::getGlobalCache( $cache_name );

        if ( isset($_REQUEST['referring_branch']) ) {
            Appcaching::setGlobalCache( $cache_name, 'ref-my-matches' );
            $ref_name = 'ref-my-matches';
        } else if ( $cached_value ) {
            $ref_name = $cached_value;
        }

        $id = 'report';
        if ( $ref_name ) {
            $id = 'report|' . $ref_name;
        }

        $this->getProfileInfo();

        $report_text = $this->getTextbutton('{#report_user#}', array( 'id' => 'report', 'style' => 'report-user-button' ));

        if ($this->menuid == 'report') {
            $report_text = $this->getText( '{#user_reported#}', array( 'style' => 'report-user-button' ));
        }

        $this->data->footer[] = $this->getRow(array(
            $this->getImage( 'attention-icon.png', array( 'margin' => '0 10 0 0', 'height' => '20' ) ),
            $report_text
        ), array( 'padding' => '8 0 8 0', 'vertical-align' => 'middle', 'text-align' => 'center', 'height' => '34' ));

    }

    public function showProfileEdit(){

        $this->rewriteActionConfigField('background_color', '#F8F8FF');
        $this->user_vars = $this->varcontent;

        $this->setGridWidths();
        $this->validateAndSave();

        /* set the screen pixels */
        $total_margin = $this->margin . ' ' . $this->margin . ' ' . $this->margin . ' ' . $this->margin;
        
        if($this->menuid == 'del-images'){
            $user[] = $this->getText('{#click_on_images_to_delete#}',array('vertical-align' => 'top','font-size' => '22'));
            $this->data->scroll[] = $this->getRow($user,array('margin' => $total_margin, 'vertical-align' => 'middle'));
            $this->deleting = true;
        } else {
            /* top part */
            $txt = $this->getVariable('real_name');

            $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );
                
            $country = '';
            $city = ThirdpartyServices::translateString( $this->getVariable('city'), 'en', $tr_lang );
            if ( $this->getVariable('country') ) {
                $country = ThirdpartyServices::translateString( $this->getVariable('country'), 'en', $tr_lang );
            }

            $user[] = $this->getText($txt, array('font-size' => '22'));
            // $user[] = $this->getText($txt2, array('font-size' => '12','floating' => '1','float' => 'right'));
            $user[] = $this->getColumn(array(
                $this->getRow(array(
                    $this->getText($city, array(
                        'floating' => 1,
                        'float' => 'right',
                        'text-align' => 'right',
                    )),
                )),
                $this->getRow(array(
                    $this->getText($country, array(
                        'floating' => 1,
                        'float' => 'right',
                        'text-align' => 'right',
                    ))
                )),
            ), array(
                'text-align' => 'right',
            ));

            $this->data->scroll[] = $this->getRow($user,array('margin' => $total_margin, 'vertical-align' => 'middle'));
        }

        $this->data->scroll[] = $this->getGrid();

        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';

        if($this->menuid == 'del-images') {
            $onclick->id = 'cancel-del';
        } else {
            $onclick->id = 'del-images';
        }

        $titlecol = array();
        if($this->getSavedVariable('profilepic2')) {
            $titlecol[] = $this->getImage('del-photos.png', array('height' => '30', 'vertical-align' => 'bottom', 'floating' => '1', 'float' => 'right', 'font-size' => '16', 'onclick' => $onclick));
        }
        $this->data->scroll[] = $this->getRow($titlecol,array('margin' => '10 '.$this->margin.' 0 '.$this->margin));

        $this->data->scroll[] = $this->formkitField('real_name','{#nickname#}','{#your_real_name#}','name');
        $this->data->scroll[] = $this->formkitField('email','{#email#}','{#your_email#}','email');

        $this->edit_type = 'popup';
        $this->edit_type_about = 'inline';
        $this->getProfileInfo( $show_user_edit = true );

        $this->data->scroll[] = $this->getText('{#saving_can_take_a_while#}',array('margin' => '10 24 10 24','text-align' => 'center','font-size' => 14));
        $this->data->footer[] = $this->saveButton();

    }

    public function getProfileInfo( $show_user_edit = false  ) {
        
        if ( !isset($this->user_vars['gender']) OR empty($this->user_vars['gender']) ) {
            return false;
        }

        $edit_action_id = $this->getConfigParam( 'detail_view' );

        $about_var_data = array(
            'profile_comment' => 'DESCRIPTION',
            'gender' => 'GENDER',
            'is_transgender' => 'TRANSGENDER',
            'sexual_orientaion' => 'SEXUAL ORIENTATION',
        );

        // Do not allow changing gender and sexual orientation
        if ( $this->edit_type_about == 'inline' ) {
            $about_var_data['profile_comment'] = '';
            unset( $about_var_data['gender'] );
            unset( $about_var_data['is_transgender'] );
            unset( $about_var_data['sexual_orientaion'] );
        }

        $this->data->scroll[] = $this->formkitBox(array(
            'title' => '{#about_me#}',
            'edit_type' => $this->edit_type_about,
            'hide_edit_icon' => true,
            'user_vars' => $this->user_vars,
            'variables' => $about_var_data,
        ));

        if ( $show_user_edit ) {
            $this->data->scroll[] = $this->formkitBox(array(
                'title' => '{#my_preferences#}',
                'edit_type' => $this->edit_type,
                'user_vars' => $this->user_vars,
                'edit_action_id' => $edit_action_id,
                'variables' => array(
                    'gender' => 'GENDER',
                    'sexual_orientaion' => 'SEXUAL ORIENTATION',
                    'is_transgender' => 'TRANSGENDER',
                ),
            ));
        }

        if ( $this->edit_type != 'popup' ) { 
            $this->data->scroll[] = $this->formkitBox(array(
                'title' => '{#searching_for#}',
                'edit_type' => $this->edit_type,
                'user_vars' => $this->user_vars,
                'edit_action_id' => $edit_action_id,
                'variables' => array(
                    'gender_preferences' => 'GENDER',
                    'sexual_preferences' => 'SEXUAL ORIENTATION',
                    'transgender_preferences' => 'TRANSGENDER',
                    'filter_age_start' => 'AGE MIN',
                    'filter_age_end' => 'AGE MAX',
                ),
            ));
        }

        $this->data->scroll[] = $this->formkitBox(array(
            'title' => '{#summary#}',
            'edit_type' => $this->edit_type,
            'user_vars' => $this->user_vars,
            'edit_action_id' => $edit_action_id,
            'variables' => array(
                'education' => 'EDUCATION',
                'languages' => 'LANGUAGES',
                'living_data' => 'JOB',
            ),
        ));

        $this->data->scroll[] = $this->formkitBox(array(
            'title' => '{#habits#}',
            'edit_type' => $this->edit_type,
            'user_vars' => $this->user_vars,
            'edit_action_id' => $edit_action_id,
            'variables' => array(
                'exercise_data' => 'EXCERCISES',
                'smoke_data' => 'SMOKES',
                'drinks_data' => 'DRINKS',
                'hobby_data' => 'HOBBIES',
                'sports_data' => 'SPORTS',
            ),
        ));
        
    }

    public function validateAndSave() {

        if(strstr($this->menuid,'imgdel-')){
            $del = str_replace('imgdel-','',$this->menuid);
            if($del == 'profilepic'){
                $this->bumpUpProfilePics();
            } else {
                $this->deleteVariable($del);
                $this->loadVariableContent(true);
            }
        }

        if ( $this->menuid != 'save-data' ) {
            return false;
        }

        $this->validateCommonVars();

        if ( empty($this->error) ) {
            $this->saveVariables();
            $this->loadVariableContent(true);
        } else {
            // Display the error messages in the footer section
            $this->displayErrors();
        }

    }
    
}