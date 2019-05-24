<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobileuserprofile.models.*');

class MobileuserprofileController extends ArticleController {

    public $data;

    public $configobj;
    public $theme;
    public $profileid;

    public $margin;
    public $grid;
    public $mode;
    public $cachename;

    public $deleting;

    /* instead of using playid and gameid as usual, it is possible to associate
       everything to another play. This is to facilitate communication between
       two apps.
    */

    public $current_playid;
    public $current_gid;

    public $error_messages = array();
    
    public function init(){

        /* we can be borrowing the data from another app */
        $this->fakePlay();
    }

    public function tab1(){

        $this->data = new StdClass();
        $this->mode = $this->requireConfigParam('mode');
        $this->cachename = $this->playid .'-currentprofileview';

        if(isset($this->params['mainswipe'])){
            $this->profileid = $this->params['mainswipe'];
            Appcaching::setGlobalCache($this->cachename,$this->menuid);
        }elseif(isset($_REQUEST['context']) AND strstr($_REQUEST['context'],'profile-',$_REQUEST['context'])){
            $this->profileid = str_replace('profile-','',$_REQUEST['context']);
            Appcaching::setGlobalCache($this->cachename,$this->menuid);
        } elseif($this->menuid AND is_numeric($this->menuid)){
            $this->profileid = $this->menuid;
            Appcaching::setGlobalCache($this->cachename,$this->menuid);
        } else {
            $this->profileid = Appcaching::getGlobalCache($this->cachename);
        }

        // for debugging only!
        //$this->profileid = $this->playid;

        if($this->mode == 'edit_profile'){
            $this->showProfileEdit();
        } else {
            $this->showProfileView();
        }

        return $this->data;
    }

    public function facebookFriends($vars){

        if(!isset($vars['fb_token']) OR !$this->getSavedVariable('fb_token')){
            return false;
        }

        if(!$vars['fb_token']){
            return false;
        }

        /* friends of the other user */
        $friends = ThirdpartyServices::getUserFbFriends($vars['fb_token'],$this->appinfo->fb_api_id,$this->appinfo->fb_api_secret);

        /* current users's friends */
        $my_friends = ThirdpartyServices::getUserFbFriends($this->getSavedVariable('fb_token'),$this->appinfo->fb_api_id,$this->appinfo->fb_api_secret);
        
        foreach ($my_friends as $myfriend){
            $id = $myfriend['id'];
            $list_my_friends[$id] = true;
        }

        if($friends){
            $count = count($friends);
            $usercount = 1;
            $round = 1;

            foreach ($friends as $friend){
                $id = $friend['id'];

                /* includes only common friends */
                if(isset($list_my_friends[$id])) {
                    $filename = 'fb_' . $id . '.jpg';
                    if (isset($friend['picture']['data']['url'])) {
                        $image = Controller::copyThirdPartyImage($this->gid, $filename, $friend['picture']['data']['url']);
                    } else {
                        $image = 'anonymous2.png';
                    }

                    if (isset($friend['name'])) {
                        $name = $friend['name'];
                    } else {
                        $name = '{#anonymous#}';
                    }

                    $onclick = new stdClass();
                    $onclick->action = 'open-url';
                    $onclick->action_config = 'https://facebook.com/' . $friend['id'];

                    $col[] = $this->getImage($image, array('imgwidth' => '150', 'imgheight' => '150', 'crop' => 'round', 'width' => '75', 'priority' => 9, 'onclick' => $onclick));
                    $col[] = $this->getText($name, array('font-size' => '11', 'text-align' => 'center', 'width' => '75'));
                    $items[] = $this->getColumn($col, array('margin' => '10 10 10 5', 'width' => '90'));
                    $temp = $this->getColumn($col, array('margin' => '10 10 10 5', 'width' => '90'));
                    unset($col);

                    if ($usercount == 4) {
                        $page[] = $this->getRow($items, array('margin' => '0 0 0 15'));
                        $page[] = $this->getSwipeNavi(ceil($count / 3), $round, array('navicolor' => 'black'));
                        $swipe[] = $this->getColumn($page);
                        $round++;
                        unset($items);
                        unset($page);
                        $usercount = 1;
                        // showing the last item again on the next page
                        $items[] = $temp;
                    }

                    $usercount++;
                }
            }

            if(isset($items) AND !is_float($count/3)){
                $page[] = $this->getRow($items,array('margin' => '0 0 0 15'));
                $page[] = $this->getSwipeNavi(ceil($count/3),$round,array('navicolor' => 'black'));
                $swipe[] = $this->getColumn($page,array('width' => '100%'));
            }

            if($count > 3 AND isset($swipe)){
                $this->data->scroll[] = $this->getSettingsTitle('{#common_facebook_friends#}');
                $this->data->scroll[] = $this->getSwipearea($swipe,array('width' => '100%'));
            } elseif(isset($items)) {
                $this->data->scroll[] = $this->getSettingsTitle('{#common_facebook_friends#}');
                $this->data->scroll[] = $this->getRow($items,array('text-align' => 'center'));
            }
        }
    }


    public function showProfileEdit(){
        $this->setGridWidths();
        $this->profileEditSaves();

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
                
            $txt2 = ThirdpartyServices::translateString( $this->getVariable('city'), 'en', $tr_lang );
            if ( $this->getVariable('country') ) {
                $txt2 .= ', ' . ThirdpartyServices::translateString( $this->getVariable('country'), 'en', $tr_lang );
            }

            $user[] = $this->getText($txt, array('font-size' => '22'));
            $user[] = $this->getText($txt2, array('font-size' => '12','floating' => '1','float' => 'right'));

            $this->data->scroll[] = $this->getRow($user,array('margin' => $total_margin, 'vertical-align' => 'middle'));
        }

        $this->data->scroll[] = $this->getGrid();

        //$titlecol[] = $this->getImage('toolbar-info.png',array('width' => '30','height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#about_me#}',array('height' => '30', 'margin' => '10 0 0 0','font-size' => '16'));

        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';

        if($this->menuid == 'del-images') {
            $onclick->id = 'cancel-del';
        } else {
            $onclick->id = 'del-images';
        }

        if ( $this->getImageCount() > 1 ) {
            $titlecol[] = $this->getImage('del-photos.png', array('height' => '30', 'vertical-align' => 'bottom', 'floating' => '1', 'float' => 'right', 'font-size' => '16', 'onclick' => $onclick));
        }

        $this->data->scroll[] = $this->getRow($titlecol,array('margin' => '10 '.$this->margin.' 0 '.$this->margin));

        $this->data->scroll[] = $this->getTextArea();
        $this->data->scroll[] = $this->getText('{#saving_can_take_a_while#}',array('margin' => '10 24 10 24','text-align' => 'center','font-size' => 14));
        $this->data->scroll[] = $this->saveButton();

    }

    public function setGridWidths(){
        $width = $this->screen_width ? $this->screen_width : 320;
        $this->margin = 20;
        $this->grid = $width - ($this->margin*4);
        $this->grid = round($this->grid / 3,0);
    }

    public function saveButton(){
        if($this->menuid == 'save-data'){
            $this->initMobileMatching();
            $this->mobilematchingobj->turnUserToItem(false,__FILE__);
            $cachename = 'uservars-'.$this->playid;
            Appcaching::removeGlobalCache($cachename);
            return $this->getTextbutton('{#saved#}',array('id' => 'save-data', 'style' => 'general_button_style_red'));
        } else {
            return $this->getTextbutton('{#save#}',array('id' => 'save-data', 'style' => 'general_button_style_red'));
        }
    }

    public function getGrid(){
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

        return $this->getRow($column,array('margin' => '0 ' .$this->margin .' 0 ' .$this->margin));
    }

    public function getTextArea(){
        $textareastyle['margin'] = '10 '.$this->margin.' 10 '.$this->margin;
        $textareastyle['padding'] = '5 5 5 5';
        $textareastyle['border-width'] = 1;
        $textareastyle['border-color'] = '#d8d8d8';
        $textareastyle['font-size'] = '13';
        $textareastyle['variable'] = $this->getVariableId('profile_comment');

        $text = '';
        if ( !empty($this->getVariable('profile_comment')) ) {
            $text = $this->getVariable('profile_comment');
        }

        return $this->getFieldtextarea($text, $textareastyle);
    }

    public function profileEditSaves(){
        if($this->menuid == 'save-data') {
            $this->saveVariables();
            $this->loadVariableContent(true);
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
    }


    /* if user deletes the first profile pic, we use this to bump up all profile pics */

    public function bumpUpProfilePics(){
        $count = 1;

        while($count < 8){
            if($count == 1){
                $existing[] = $this->getSavedVariable('profilepic');
            } else {
                if($this->getSavedVariable('profilepic'.$count)){
                    $existing[] = $this->getSavedVariable('profilepic'.$count);
                }
            }
            $count++;
        }

        if(isset($existing) AND is_array($existing) AND count($existing) > 1){
            $last = count($existing);
            $this->deleteVariable('profilepic'.$last);
            array_shift($existing);
            $count = 1;

            foreach($existing as $val){
                if($val){
                    if($count == 1){
                        $this->saveVariable('profilepic',$val);
                    } else {
                        $this->saveVariable('profilepic'.$count,$val);
                    }
                }
                $count++;
            }

        }
    }

    public function doReporting(){
        if ( $this->menuid == 'report' ) {
            $back_to_action = $this->getConfigParam('matching_action');

            // My Matches
            $this->mobilematchingobj->reportUser();
            $cmd = new StdClass();
            $cmd->action = 'open-action';
            $cmd->action_config = $back_to_action;
            $cmd->sync_open = 1;
            $this->data->onload = array($cmd);
        }
    }

    public function profileCommentView($vars){
        // $titlecol[] = $this->getImage('toolbar-info.png',array('width' => '30','height' => '30', 'margin' => '10 0 0 0'));
        // $titlecol[] = $this->getText('{#about#} '.$this->getFirstName($vars),array('height' => '30', 'margin' => '10 0 0 0','font-size' => '13'));
        // $this->data->scroll[] = $this->getRow($titlecol,array('width' => '100%','margin' => '10 0 0 12'));

        $textareastyle['margin'] = '10 24 10 20';
        $textareastyle['font-size'] = '16';

        $textareastyle['variable'] = $this->getVariableId('profile_comment');
        if(isset($vars['profile_comment'])){
            $this->data->scroll[] = $this->getText($vars['profile_comment'],$textareastyle);
        }
    }

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

        $txt2 = $city;
        if ( $country ) {
            $txt2 .= ', ' . $country;
        }

        $user[] = $this->getText($txt,array('height' => '30', 'margin' => '15 20 0 20','font-size' => '22'));
        $user[] = $this->getText($txt2,array('height' => '30', 'margin' => '21 23 0 20', 'font-size' => '16', 'floating' => 1, 'float' => 'right'));

        $this->data->scroll[] = $this->getRow($user, array( 'vertical-align' => 'middle' ));

        if(!isset($vars['real_name'])){
            $this->data->scroll[] = $this->getText('{#info_missing#}');
            return true;
        }

        $this->profileCommentView($vars);

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

        // $this->data->footer[] = $this->getText( $id );
        $this->data->footer[] = $this->getTextbutton('{#report_user#}', array( 'id' => 'report', 'style' => 'report-user-button' ));

        if ($this->menuid == 'report') {
            $this->data->footer[] = $this->getText( '{#user_reported#}', array( 'text-align' => 'center', 'padding' => '0 5 8 5', 'font-size' => '13' ));
        }

    }

    public function getImageScroll($vars){

        if(!isset($vars['profilepic'])){
            return $this->getImage('anonymous2.png', array( 'margin' => '5 30 0 30' ));
        }

        $count = 1;
        $params['imgwidth'] = '600';
        $params['imgheight'] = '600';
        $params['imgcrop'] = 'yes';
        $params['height'] = $this->screen_width;
        $params['width'] = $this->screen_width;
        $params['not_to_assetlist']  = true;
        $params['priority'] = 9;

        $swipnavi['margin'] = '-35 0 0 0';
        $swipnavi['align'] = 'center';
        $totalcount = 1;

        while($count < 10){
            $n = 'profilepic' .$count;
            if(isset($vars[$n])){
                $path = $_SERVER['DOCUMENT_ROOT'] .$vars[$n];
            }

            if(isset($vars[$n]) AND strlen($n) > 2 AND isset($path) AND file_exists($path) AND filesize($path) > 40){
                $totalcount++;
            }
            $count++;
        }

        $count = 1;
        $mycount = 1;

	    $zoom_image = $this->getImage($vars['profilepic'], array(
		    'imgwidth' => '900',
		    'priority' => 9,
	    ));

        $main_image_params = array_merge($params, array(
        	'tap_image' => $zoom_image->content
        ));
        $scroll[] = $this->getImage($vars['profilepic'], $main_image_params);

        $scroll[] = $this->getSwipeNavi($totalcount,1,$swipnavi);
        $item[] = $this->getColumn($scroll,array('width' => '100%'));
        unset($scroll);

        while($count < 10){
            $n = 'profilepic' .$count;
            if(isset($vars[$n]) AND strlen($n) > 2){
                $path = $_SERVER['DOCUMENT_ROOT'] .$vars[$n];

                if(file_exists($path) AND filesize($path) > 40){
                    $mycount++;

	                $zoom_image = $this->getImage($vars[$n], array(
		                'imgwidth' => '900',
		                'priority' => 9,
	                ));

                    $params['tap_image'] = $zoom_image->content;
                    $scroll[] = $this->getImage($vars[$n],$params);
                    $scroll[] = $this->getSwipeNavi($totalcount,$mycount,$swipnavi);
                    $item[] = $this->getColumn($scroll,array());
                    unset($scroll);
                }
            }
            $count++;
        }

        return $this->getSwipearea($item);
    }

    public function getFirstName($vars){

        if ( isset($vars['screen_name']) ) {
            return $vars['screen_name'];
        }

        if ( isset($vars['real_name']) ) {
            $firstname = explode(' ',trim($vars['real_name']));
            $firstname = $firstname[0];
            return $firstname;
        }
        
        return '{#anonymous#}';
    }

    public function getProfileImage($name, $mainimage = false){

        if($mainimage){
            $params['width'] = $this->grid*2 + $this->margin;
            $params['height'] = $this->grid*2 + $this->margin;
            $params['imgwidth'] = '600';
            $params['imgheight'] = '600';
        } else {
            $params['width'] = $this->grid;
            $params['height'] = $this->grid;
            $params['imgwidth'] = '600';
            $params['imgheight'] = '600';
        }

        $params['imgcrop'] = 'yes';
        $params['crop'] = ( isset($crop) ? $crop : 'yes' );
        $params['defaultimage'] = 'profile-add-photo-grey.png';

        if($this->deleting AND $this->getSavedVariable($name) AND strlen($this->getSavedVariable($name)) > 2){
            $params['opacity'] = '0.6';
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'submit-form-content';
            $params['onclick']->id = 'imgdel-'.$name;
        } else {
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'upload-image';
            $params['onclick']->max_dimensions = '600';
            $params['onclick']->variable = $this->getVariableId($name);
            $params['onclick']->action_config = $this->getVariableId($name);
            $params['onclick']->sync_upload = true;
        }

        $params['variable'] = $this->getVariableId($name);
        $params['config'] = $this->getVariableId($name);
        $params['debug'] = 1;
        $params['fallback_image'] = 'selecting-image.png';
        $params['priority'] = 9;

        return $this->getImage($this->getVariable($name),$params);
    }

    public function getImageCount() {
        $count = 0;

        foreach ($this->varcontent as $var => $content) {
            if ( preg_match('~profilepic~', $var) ) {
                $count++;
            }
        }

        return $count;
    }

    /*
    * This function would validate the name and email fields
    * returns TRUE if valid, FALSE if invalid
    */
    public function validateCommonVars() {

        $email_var = $this->getVariableId( 'email' );
        $name_var = $this->getVariableId( 'real_name' );

        $validator = new CEmailValidator;
        $validator->checkMX = true;

        if ( empty($this->submitvariables[$email_var]) ) {
            $this->error = true;
            $this->error_messages[] = '{#the_email_field_could_not_be_blank#}';
        } else if ( !$validator->validateValue($this->submitvariables[$email_var]) ) {
            $this->error = true;
            $this->error_messages[] = '{#please_enter_a_valid_email_address#}';
        }

        if ( empty($this->submitvariables[$name_var]) ) {
            $this->error = true;
            $this->error_messages[] = '{#the_name_field_could_not_be_blank#}';
        }

    }

    public function displayErrors() {

        if ( empty($this->error_messages) OR !is_array($this->error_messages) ) {
            return false;
        }

        foreach ($this->error_messages as $message) {
            $this->data->footer[] = $this->getText($message, array('style' => 'register-text-step-2'));
        }

        return true;
    }

    public function validateAndSave() {

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