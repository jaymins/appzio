<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

class oliveMobileuserprofileSubController extends MobileuserprofileController {

    public $availability;
    public $mobileplacesobj;

    public function showProfileView(){

        /* determine who's profile should we show */
        $this->initMobileMatching();
        $this->mobilematchingobj->initMatching($this->profileid);
        $this->doReporting();

        $vars = AeplayVariable::getArrayOfPlayvariables($this->profileid);
        $this->data->scroll[] = $this->getImageScroll($vars);

        $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );

        $txt = $this->getFirstName($vars);
        $city = isset($vars['city']) ? ThirdpartyServices::translateString( $vars['city'], 'en', $tr_lang ) : '{#hidden_city#}';
        $country = isset($vars['country']) ? ThirdpartyServices::translateString( $vars['country'], 'en', $tr_lang ) : '{#hidden_country#}';

        $right_data[] = $this->getRow(array(
            $this->getText( $city )
        ), array( 'text-align' => 'right' ));

        if ( $country ) {
            $right_data[] = $this->getRow(array(
                $this->getText( $country )
            ), array( 'text-align' => 'right' ));
        }

        // Rewrite the subject
        $this->rewriteActionField('subject', $txt);

        $user[] = $this->getText($txt, array(
            'width' => '50%',
            'color' => '#12859f',
            'padding' => '0 0 0 20',
            'font-size' => '22',
        ));

        $user[] = $this->getColumn($right_data, array(
            'width' => '50%',
            'padding' => '0 20 0 0',
            'font-size' => '16',
        ));

        $this->data->scroll[] = $this->getRow($user, array( 'vertical-align' => 'middle', 'padding' => '5 0 5 0' ));

        $this->data->scroll[] = $this->getSpacer( 1, array( 'margin' => '0 0 20 0', 'background-color' => '#b4b2b2' ) );

        if(!isset($vars['screen_name'])){
            $this->data->scroll[] = $this->getText('{#info_missing#}');
            return true;
        }

        $textareastyle['variable'] = $this->getVariableId('profile_comment');
        if ( isset($vars['profile_comment']) AND !empty($vars['profile_comment']) ) {
            $this->data->scroll[] = $this->getText($vars['profile_comment'], array( 'style' => 'olive-profile-description' ));
        }

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

        $interests = $this->getVariable( 'interests' );
        $interests = json_decode( $interests );
        
        $interests_data = array();
        $row = array();
        $counter = 0;

        if ( $interests ) {
            
            foreach ($interests as $key => $value) {
                if ( empty($value) ) {
                    continue;
                }

                $interest = ucfirst( str_replace('_', ' ', $key) );
                $interests_data[] = $this->getText( $interest, array( 'style' => 'formkit-radiobutton-selected' ) );
            }
            
            foreach ($interests_data as $item){
                /* 27 is the width with paddings and margins */
                $counter = $counter + 27;
                $counter = $counter + (strlen($item->content)*6.5);

                if($counter > $this->screen_width){
                    $row[] = $this->getVerticalSpacer('');
                    $this->data->scroll[] = $this->getRow($row);
                    unset($row);
                    $row[] = $item;
                    $counter=0;
                    $counter = $counter + 27;
                    $counter = $counter + (strlen($item->content)*6.5);
                } else {
                    $row[] = $item;
                }
            }

            if ( !empty($row) ) {
                $row[] = $this->getVerticalSpacer('');
                $this->data->scroll[] = $this->getRow($row);
            }
            
        }

    }

    public function showProfileEdit(){

        if ( $this->getSubmittedVariableByName('screen_name') AND $this->getSubmittedVariableByName('screen_name') != $this->getSavedVariable('screen_name') ) {
            $varid = $this->getVariableId('screen_name');
            $ob = AeplayVariable::model()->findByAttributes(array('variable_id' => $varid,'value' => $this->getSubmittedVariableByName('screen_name')));

            if(is_object($ob) AND isset($ob->id)){
                $error = '{#screen_name_is_taken#}';
            } else {
                $error = false;
            }
        } else {
            $error = false;
        }

        if ( $this->menuid == 'save-data' AND !$error ) {
            $this->adjustGenderPreferences();
            $this->saveVariables();
            $this->loadVariableContent(true);
        }

        $tttst['margin'] = '10 20 10 20';
        $tttst['text-align'] = 'center';
        $tttst['font-size'] = '14';

        /* set the screen pixels */
        $width = $this->screen_width ? $this->screen_width : 320;
        $this->margin = 20;
        $this->grid = $width - ($this->margin*4);
        $this->grid = round($this->grid / 3,0);
        $total_margin = $this->margin . ' ' . $this->margin . ' ' . $this->margin . ' ' . $this->margin;

        if($this->menuid == 'del-images'){
            $user[] = $this->getText('{#click_on_images_to_delete#}',array('vertical-align' => 'top','font-size' => '22'));
            $this->data->scroll[] = $this->getRow($user,array('margin' => $total_margin, 'vertical-align' => 'middle'));
            $this->deleting = true;
        } else {
            /* top part */
            $txt = $this->getVariable('screen_name');

            $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );

            $txt2 = ThirdpartyServices::translateString( $this->getVariable('city'), 'en', $tr_lang );
            if ( $this->getVariable('country') ) {
                $txt2 .= ', ' . ThirdpartyServices::translateString( $this->getVariable('country'), 'en', $tr_lang );
            }

            $user[] = $this->getText($txt, array('font-size' => '22'));
            $user[] = $this->getText($txt2, array('font-size' => '15','floating' => '1','float' => 'right'));

            $this->data->scroll[] = $this->getRow($user,array('margin' => $total_margin, 'vertical-align' => 'middle'));
        }

        if(strstr($this->menuid,'imgdel-')){
            $del = str_replace('imgdel-','',$this->menuid);
            if($del == 'profilepic'){
                $this->bumpUpProfilePics();
            } else {
                $this->deleteVariable($del);
                $this->loadVariableContent(true);
            }
        }

        /* row 1 with big picture and two small ones */

        $column[] = $this->getProfileImage('profilepic',true);
        $column[] = $this->getVerticalSpacer($this->margin);

        $row[] = $this->getProfileImage('profilepic2');
        $row[] = $this->getSpacer($this->margin);
        $row[] = $this->getProfileImage('profilepic3');

        $column[] = $this->getColumn($row);

        $this->data->scroll[] = $this->getRow($column,array('margin' => '0 ' .$this->margin .' 0 ' .$this->margin));
        $this->data->scroll[] = $this->getSpacer($this->margin);

        unset($column);
        unset($row);

        $column[] = $this->getProfileImage('profilepic4');
        $column[] = $this->getVerticalSpacer($this->margin);
        $column[] = $this->getProfileImage('profilepic5');
        $column[] = $this->getVerticalSpacer($this->margin);
        $column[] = $this->getProfileImage('profilepic6');

        $this->data->scroll[] = $this->getRow($column,array('margin' => '0 ' .$this->margin .' 0 ' .$this->margin));


        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';

        if($this->menuid == 'del-images') {
            $onclick->id = 'cancel-del';
        } else {
            $onclick->id = 'del-images';
        }

        if($this->getSavedVariable('profilepic2')) {
            $titlecol[] = $this->getImage('del-photos.png', array('height' => '30', 'vertical-align' => 'bottom', 'floating' => '1', 'float' => 'right', 'font-size' => '16', 'onclick' => $onclick));
        }

        $textareastyle['margin'] = '10 '.$this->margin.' 10 '.$this->margin;
        $textareastyle['border-width'] = 1;
        $textareastyle['border-color'] = '#d8d8d8';
        $textareastyle['font-size'] = '13';

        $textareastyle['variable'] = $this->getVariableId('profile_comment');


        // Profile info
        $this->getProfileInfo( $error );

        $this->data->scroll[] = $this->getText('{#saving_can_take_a_while#}',$tttst);

        if($this->menuid == 'save-data'){
            $this->initMobileMatching();
            $this->mobilematchingobj->turnUserToItem(false,__FILE__);
            $this->data->scroll[] = $this->getTextbutton('{#saved#}',array('style' => 'olive-submit-button','id' => 'save-data'));
        } else {
            $this->data->scroll[] = $this->getTextbutton('{#save#}',array('style' => 'olive-submit-button','id' => 'save-data'));
        }

    }

    public function getProfileInfo( $error ) {

        $fields[] = $this->formkitTextarea('profile_comment','{#about_me#}','{#public_profile_description#}');

        $this->data->scroll[] = $this->getColumn($fields,array('margin' => '15 0 0 0'));

        // $columns[] = $this->getImage('actionimage1',array('actionimage' => true,'width' => '10%'));

        $this->data->scroll[] = $this->addField_imate('screen_name','{#nickname#}','{#your_screen_name#}','nickname',$error);
        $this->data->scroll[] = $this->addField_imate('email','{#email#}','{#your_email#}','email');
        if ($error) {
            $this->data->scroll[] = $this->getText($error,array('style' => 'register-text-step-error'));
        }
        $this->data->scroll[] = $this->addField_imate('phone','{#phone#}','{#your_phone#}','phone');

        $this->data->scroll[] = $this->formkitCheckbox('look_for_men', '{#show_men#}', array(
            'type' => 'toggle',
        ));
        $this->data->scroll[] = $this->formkitCheckbox('look_for_women', '{#show_women#}', array(
            'type' => 'toggle',
        ));

        // $this->data->scroll[] = $this->addField_imate('email','{#email#}','{#your_email#}');
        // $this->data->scroll[] = $this->getImage('hairline-divider.png',array('height' => '1px'));

        $cities = array('Aurora' => 'Aurora','Markham' => 'Markham','Newmarket' => 'Newmarket','Richmond Hill' => 'Richmond Hill','Vaughan' => 'Vaughan',
            'Scarborough' => 'Scarborough', 'North York' => 'North York', 'Toronto' => 'Toronto', 'Mississauga' => 'Mississauga');

        $listparams['variable'] = 'city';
        $this->data->scroll[] = $this->formkitRadiobuttons('{#location#}',$cities,$listparams);

        $this->showInterestsBlock();

    }

    public function showInterestsBlock() {
        $listparams['variable'] = 'interests';
        
        //$listparams['content'] = json_decode($this->getSavedVariable('interests'),true);
        $interests = array('japanese_food'=>'{#japanese#}','chinese_food'=>'{#chinese#}','deserts' => '點心', 'buffet_food' => '{#buffet#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#food_and_tasting#}',$interests,$listparams);
        $interests = array('tennis'=>'{#tennis#}','volleyball'=>'{#volleyball#}','basketball' => '{#basketball#}','sport_chinese' => '羽毛球');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#sport#}',$interests,$listparams);
        $interests = array('offroad'=>'{#offroad#}','sports_cars'=>'{#sports_car#}','motorbikes' => '{#motorbike#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#automobile#}',$interests,$listparams);
        $interests = array('gardening'=>'{#gardening#}');
        $this->data->scroll[] = $this->formkitTags('{#interests#}: {#others#}',$interests,$listparams);

        $this->data->scroll[] = $this->getSpacer(10);
    }

    public function addField_imate($name,$title,$hint,$type=false,$error=false){
        $param = $this->getVariableId($name);
        if($error){
            $content = $this->getSubmittedVariableByName($name);
        } else {
            $content = $this->getSavedVariable($name);
        }
        $col[] = $this->getText(strtoupper($title),array('style' => 'form-field-titletext'));

        if($error){
            $style = 'form-field-textfield';
            $style_separator = 'form-field-separator-error';
        } else {
            $style = 'form-field-textfield';
            $style_separator = 'form-field-separator';
        }

        if($type){
            $col[] = $this->getFieldtext($content,array('variable' => $param,'hint' => $hint,'style' => $style,'input_type' => $type));
        } else {
            $col[] = $this->getFieldtext($content,array('variable' => $param,'hint' => $hint,'style' => $style));
        }

        $col[] = $this->getText('',array('style' => $style_separator));
        return $this->getColumn($col,array('style' => 'form-field-row'));
    }

    public function adjustGenderPreferences() {
        
        if($this->getSubmittedVariableByName('look_for_men')){
            $this->saveVariable('men',$this->getSubmittedVariableByName('look_for_men'));
        } else {
            $this->deleteVariable('men');
        }

        if($this->getSubmittedVariableByName('look_for_women')){
            $this->saveVariable('women',$this->getSubmittedVariableByName('look_for_women'));
        } else {
            $this->deleteVariable('women');
        }

    }

    // Pretty sure we could go without this method
    public function saveVariables($exclude = false){

        /* saving tags */
        $varid = $this->getVariableId('interests');

        foreach($this->submitvariables as $key=>$val){
            
            if(stristr($key,$varid.'_')){
                $id = str_replace($varid.'_','',$key);
                $savearray[$id] = $val;
                unset($this->submitvariables[$key]);
            }
        }

        if(isset($savearray)){
            $this->submitvariables[$varid] = json_encode($savearray);
        }

        ArticleModel::saveVariables($this->submitvariables,$this->playid);
        $this->loadVariableContent();
        return true;
    }

}