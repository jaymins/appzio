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

Yii::import('application.modules.aechat.models.*');

class MobilechatController extends ArticleController {

    public $configobj;

    public $context_key;
    public $data;
    public $participant_count;
    public $chat_type = 'default';
    public $chat_id;

    /* @var Aechat */
    public $chat_obj;
    public $expended_connection_ids;

    public $disable_bottom_notifications = true;

    /* 
    * instead of using playid and gameid as usual, it is possible to associate
    * everything to another play. This is to facilitate communication between
    * two apps.
    */
    public $current_playid;
    public $current_gid;

    public function init(){
        /* we can be borrowing the data from another app */
        $this->fakePlay();
    }

    /* this gets called when doing branchlisting */
    public function tab1(){

        $this->initMobileChat(false,false);

        $this->attachParameters();

        if($this->menuid == 'new-group-chat'){
            return $this->newGroupChat();
        }

        switch($this->getConfigParam('mode')){
            case 'group_chat_listing':
                return $this->groupChatListing();
                break;

            default:
                return $this->individualChat();
                break;

        }

    }

    public function groupChatListing(){
        $this->data = new stdClass();

        $params['mode'] = 'my_owned_chats';
        $this->data->scroll[] = $this->getHeading('{#chats_i_host#}');
        $this->data->scroll[] = $this->moduleGroupChatList($params);

        $params['mode'] = 'my_chats';
        $this->data->scroll[] = $this->getHeading('{#chats_im_member_of#}');
        $this->data->scroll[] = $this->moduleGroupChatList($params);

        $params['mode'] = 'public_chats';
        $this->data->scroll[] = $this->getHeading('{#public_chats#}');
        $this->data->scroll[] = $this->moduleGroupChatList($params);

        $this->data->footer[] = $this->getButtonWithIcon('icon-new-white.png','new-group-chat','{#add_a_new_chat#}',array('id' => 'new-chat','style' => 'general_button_style_red'),array('color' => '#ffffff','font-size' => '14'));
        
        // $this->data->footer[] = $this->getSpacer('20');
        return $this->data;
    }

    public function individualChat(){
        $args = array();
        $this->rewriteActionConfigField( 'keep_scroll_in_bottom', 1 );
        $this->rewriteActionConfigField( 'poll_update_view', 'scroll' );
        $this->rewriteActionConfigField( 'poll_interval', '1' );

        /* this basically means that its suited for dating app */
        if ( $this->getConfigParam('use_referrer') ) {

            if ( isset($this->menuid) AND isset($_REQUEST['referring_action']) ) {
                $args = $this->setCache($this->menuid);
            } else {
                $cache = Appcaching::getGlobalCache( $this->getCacheName() );
                if ( $cache ) {
                    $args = $cache;
                } else {
                    $data = new StdClass();
                    $data->scroll[] = $this->getText('Initialisation error');
                    return $data;
                }
            }

            if ( $this->menuid != 'submit-msg' AND $this->menuid != 'get-next-page' ) {
                // Always reset the page number upon opening the module
                $this->saveVariable( 'tmp_chat_page', 1 );
            }

        } else {
            $cache = Appcaching::getGlobalCache( $this->getCacheName() );

            if ( isset($this->menuid) AND !empty($this->menuid) AND
                $this->menuid != 'submit-msg' AND
                $this->menuid != 'get-next-page'
            ) {              
                // Set the Context Key
                $context_key = $this->menuid;

                // Failsafe
                if ( empty($cache) ) {
                    $cache = array();
                }

                if ( !is_array($cache) ) {
                    $cache = (array) $cache;
                }

                $cache[] = $this->menuid;
                Appcaching::setGlobalCache( $this->getCacheName(), $cache );

                // Always reset the page number upon opening the module
                $this->saveVariable( 'tmp_chat_page', 1 );
            }

            if ( !empty($cache) ) {
                // Get the last known "chat room"
                $context_key = end($cache);
            }

            if ( empty($context_key) ) {
                $context_key = $this->action_id;
            }

            $args['context_key'] = $context_key;
            $args['context'] = 'action';
        }


        $args = $this->getAdditionalArguments($args);


        $data = $this->moduleChat( $args );

        if ( $this->getChatDivs() ) {
            $data->divs = $this->getChatDivs();
        }

        return $data;
    }

    public function getAdditionalArguments($args){
        $args['strip_urls'] = $this->getConfigParam('strip_urls');
        $args['pic_permission'] = $this->getConfigParam('pic_permission');
        $args['firstname_only'] = $this->getConfigParam('firstname_only');
        $args['hide_time'] = $this->getConfigParam('hide_time');
        $args['limit_monologue'] = $this->getConfigParam('limit_monologue');
        $args['can_invite_others'] = $this->getConfigParam('can_invite_others');
        $args['disable_header'] = $this->getConfigParam('disable_header');
        $args['use_server_time'] = $this->getConfigParam( 'use_server_time' );

        if ( !isset($args['chat_id']) AND $this->getConfigParam('context_chat_id') ) {
            $args['chat_id'] = $this->getConfigParam('context_chat_id');
        }

        return $args;
    }

    public function newGroupChat(){
        $this->createNewGroupChat($this->current_playid,'group-public');
        $this->setCache($this->context_key);
        return $this->openChat();
    }

    public function openChat(){

        $data = new stdClass();
        $openchat = new stdClass();
        $openchat->action = 'open-action';
        $openchat->action_config = $this->getConfigParam('chat');
        $openchat->back_button = true;
        $openchat->sync_open = true;
        $openchat->sync_close = true;
        $openchat->viewport = 'top';
        $openchat->id = $this->context_key;

        $data->onload[] = $openchat;
        $data->onload[] = $this->getOnclick('tab2');

        return $data;

    }

    public function getCacheName(){
        return $this->current_playid . '-' . $this->userid .'-chattemp2';
    }


    public function setCache($context_key = false){
        if(!$context_key){
            $context_key = $this->menuid;
        } elseif($this->context_key){
            $context_key = $this->context_key;
        }

        $args['custom_play_id'] = $this->current_playid;
        $args['userid'] = $this->userid;
        $args['context_key'] = $context_key;
        $args['context'] = 'action';

        if(stristr($context_key,'groupchat-')){
            $this->chat_type = 'group';
            $args['userlist'] = Aechatusers::getChatUserslist($context_key);
        }

        $obj = Aechatusers::model()->findByAttributes(array('context_key' => $context_key));

        if(isset($obj->chat_id)){
            $this->chat_id = $obj->chat_id;
            $args['chat_id'] = $this->chat_id;
            $this->context_key = $context_key;
        }

        Appcaching::setGlobalCache( $this->getCacheName(), $args );

        return $args;
    }


    public function tab2(){
        $this->data = new stdClass();
        $cachename = $this->current_playid.'tab2-chat-'.$this->context_key;
        $cache = Appcaching::getGlobalCache($cachename);

        if($this->current_tab == '2') {
            $this->rewriteActionConfigField( 'keep_scroll_in_bottom', 0 );
            $this->rewriteActionConfigField( 'poll_update_view', 'scroll' );
            $this->rewriteActionConfigField( 'poll_interval', '' );

            Appcaching::removeGlobalCache('chatheader-'.$this->chat_id);
            $cache = Appcaching::getGlobalCache( $this->getCacheName() );
            $this->context_key = $cache['context_key'];
            $obj = Aechatusers::model()->findByAttributes(array('context_key' => $this->context_key));

            if(isset($obj->chat_id)){
                $this->chat_id = $obj->chat_id;
                $this->chat_obj = Aechat::model()->findByPk($obj->chat_id);
            }

            if($this->menuid == 'savechatname'){
                $saveinfo = $this->saveChatInfo();
            }

            if(stristr($this->menuid,'adduser_')){
                $saveinfo = $this->saveChatInfo();
                $this->addUser();
            } elseif(stristr($this->menuid,'kickuser_')){
                $saveinfo = $this->saveChatInfo();
                $this->kickUser();
            }

            $this->chatInfo();
            $this->showParticipants();

            $click = new stdClass();
            $click->action = 'submit-form-content';
            $click->id = 'savechatname';

            if(!isset($saveinfo)){
                $this->data->footer[] = $this->getTextbutton('{#save#}', array('id' => 'back', 'onclick' => $click));
            } elseif($saveinfo == true) {
                $this->data->footer[] = $this->getTextbutton('‹‹ {#info_saved_click_to_go_back#}', array('id' => 'back', 'onclick' => $this->getOnclick('tab1')));
            } else {
                $this->data->footer[] = $this->getText('{#name_type_location_and_interest_are_mandatory#}',array('text-size' => 12,'margin' => '10 40 10 40','text-align' => 'center','color' => '#C63314'));
                $this->data->footer[] = $this->getTextbutton('{#save#}', array('id' => 'back', 'onclick' => $click));
            }

            Appcaching::setGlobalCache($cachename,$this->data);
        } elseif($cache) {
            return $cache;
        } else {
            $this->data->scroll[] = $this->getFullPageLoader();
        }

        return $this->data;

    }

    public function checkBox($varname, $title, $error = false, $params = false){

        $row[] = $this->getText(strtoupper($title), array('style' => 'form-field-textfield-onoff'));

        $row[] = $this->getFieldonoff($this->getSavedVariable($varname),array(
                'value' => $this->getSavedVariable($varname),
                'variable' => $this->getVariableId($varname),
                'margin' => '0 15 9 0',
                'floating' => '1',
                'float' => 'right'
            )
        );

        $columns[] = $this->getRow($row);
        $columns[] = $this->getText('',array('style' => 'form-field-separator'));
        return $this->getColumn($columns, array('style' => 'form-field-row'));
    }


    public function chatInfo(){
        if(isset($this->chat_obj->owner_play_id) AND $this->chat_obj->owner_play_id == $this->current_playid AND stristr($this->context_key,'groupchat-')){

            $cities = array('Aurora' => 'Aurora','Markham' => 'Markham','Newmarket' => 'Newmarket','Richmond Hill' => 'Richmond Hill','Vaughan' => 'Vaughan',
            'Scarborough' => 'Scarborough', 'North York' => 'North York', 'Toronto' => 'Toronto', 'Mississauga' => 'Mississauga');


            $saver = new stdClass();
            $saver->id = 'savechatname';
            $saver->action = 'submit-form-content';

            $this->data->scroll[] = $this->getHeading('{#group_chat_info#}');
            $style_separator = 'form-field-separator';

            $col[] = $this->getText(strtoupper('{#name#}'),array('style' => 'form-field-titletext'));
            $row[] = $this->getFieldtext($this->chat_obj->title,array('style' => 'form-field-textfield','variable' => 'tempsaver'));
            // $row[] = $this->getImage('black-save-icon.png',array('margin' => '8 15 8 0','height' => '27','onclick'=>$saver));

            $col[] = $this->getRow($row);
            $col[] = $this->getText('',array('style' => $style_separator));
            $this->data->scroll[] = $this->getColumn($col,array('style' => 'form-field-row'));

            $param['value'] = $this->chat_obj->can_invite;
            $this->data->scroll[] = $this->formkitCheckbox('temp_others_invite','{#others_can_invite_participants#}',$param);

            if($this->chat_obj->type == 'group-public'){
                $params['value'] = 1;
            } else {
                $params = array();
            }

            $this->data->scroll[] = $this->formkitCheckbox('temp_public','{#public#}',$params);

            $types = array('event' => '{#event#}/{#activity#}','discussion' => '{#discussion#}','other' => '{#other#}');
            $listparams['variable'] = 'temp_type';
            $listparams['value'] = $this->chat_obj->category;
            $this->data->scroll[] = $this->formkitRadiobuttons('{#type#}',$types,$listparams);

            $listparams['variable'] = 'temp_location';
            $listparams['value'] = json_decode($this->chat_obj->city,true);
            $this->data->scroll[] = $this->formkitTags('{#location#}',$cities,$listparams);

            $listparams['variable'] = 'temp_interests';
            $listparams['value'] = json_decode($this->chat_obj->tags,true);
            $interests = array('japanese_food'=>'{#japanese#}','chinese_food'=>'{#chinese#}','deserts' => '點心', 'buffet_food' => '{#buffet#}');
            $this->data->scroll[] = $this->formkitTags('{#interests#}: {#food_and_tasting#}',$interests,$listparams);
            $interests = array('tennis'=>'{#tennis#}','volleyball'=>'{#volleyball#}','basketball' => '{#basketball#}','sport_chinese' => '羽毛球');
            $this->data->scroll[] = $this->formkitTags('{#interests#}: {#sport#}',$interests,$listparams);
            $interests = array('offroad'=>'{#offroad#}','sports_cars'=>'{#sports_car#}','motorbikes' => '{#motorbike#}');
            $this->data->scroll[] = $this->formkitTags('{#interests#}: {#automobile#}',$interests,$listparams);
            $interests = array('gardening'=>'{#gardening#}');
            $this->data->scroll[] = $this->formkitTags('{#interests#}: {#others#}',$interests,$listparams);

        }
    }

    /* not used at least for now */
    public function tab3(){

        if($this->current_tab == 3){
            $this->rewriteActionConfigField( 'keep_scroll_in_bottom', 0 );
            $this->rewriteActionConfigField( 'poll_update_view', 'scroll' );
            $this->rewriteActionConfigField( 'poll_interval', '' );
        }

        /* saving array by using names as keys */


        if(isset($interests)){
            $listparams['variable'] = 'filter_countries';
            $listparams['data'] = json_decode($this->getSavedVariable('filter_countries'),true);
            $listparams['title'] = '{#countries#}';
            $listparams['list_data'] = $interests;
            $listparams['tab_back'] = 2;
            return $this->getSelectorListing($listparams);
        }

    }

    public function saveChatInfo(){

        $return = false;

        if(isset($this->submitvariables['tempsaver']) AND $this->submitvariables['tempsaver']){
            $this->chat_obj->title = $this->submitvariables['tempsaver'];
            $return = true;

            if(isset($this->submitvariables['temp_public']) AND $this->submitvariables['temp_public'] == 1){
                $this->chat_obj->type = 'group-public';
            } else {
                $this->chat_obj->type = 'group';
            }

            /* saving tags */
            foreach($this->submitvariables as $key=>$val){
                if(stristr($key,'temp_interests_')){
                    $id = str_replace('temp_interests_','',$key);
                    $savearray[$id] = $val;
                }elseif(stristr($key,$this->getVariableId('temp_interests').'_')){
                    $id = str_replace($this->getVariableId('temp_interests').'_','',$key);
                    $savearray[$id] = $val;
                }
            }

            if(isset($savearray)){
                $this->chat_obj->tags = json_encode($savearray);
            } else {
                $return = false;
            }

            /* saving cities */
            foreach($this->submitvariables as $key=>$val){
                if(stristr($key,'temp_location_')){
                    $id = str_replace('temp_location_','',$key);
                    $locationsavearray[$id] = $val;
                }
            }

            if(empty($locationsavearray)){
                $return = false;
            } else {
                $this->chat_obj->city = json_encode($locationsavearray);
            }

            if(isset($this->submitvariables['temp_type']) AND $this->submitvariables['temp_type']){
                $this->chat_obj->category = $this->submitvariables['temp_type'];
            } else {
                $return = false;
            }

            if(isset($this->submitvariables['temp_others_invite'])){
                $this->chat_obj->can_invite = $this->submitvariables['temp_others_invite'];
            }

            $this->chat_obj->active = 1;
            $this->chat_obj->update();
        }

        return $return;

    }

    public function kickUser(){

        $id = str_replace('kickuser_','',$this->menuid);

        if($id AND $id != $this->current_playid){
            Aechatusers::model()->deleteAllByAttributes(array('context_key' => $this->context_key,'chat_user_play_id' => $id));
        }

    }

    public function addUser(){

        if(stristr($this->menuid,'_create_') AND !stristr($this->context_key,'groupchat-')){
            $id = str_replace('adduser_create_','',$this->menuid);
            $createchat = true;
        } elseif(stristr($this->menuid,'_create_')) {
            $id = str_replace('adduser_create_','',$this->menuid);
            $createchat = false;
        } else {
            $id = str_replace('adduser_','',$this->menuid);
            $createchat = false;
        }

        if($createchat){
            $this->createGroupChatFromExistingChat($id);
        } else {
            $this->addUserToChatFromContext($id);
        }

    }

    public function addUserToChatFromContext($id){

        /* find out the chat id first */
        $obj = Aechatusers::model()->findByAttributes(array('context_key' => $this->context_key));

        if(!isset($obj->chat_id)){
            return false;
        }

        $this->addUserToChat($obj->chat_id,$id,$this->context_key);
    }

    public function addUserToChat($chatid,$userid,$context_key){
        $this->mobilechatobj->addUserToChat($chatid,$userid,$context_key);
    }

    public function createGroupChatFromExistingChat($adduser){

        $obj = Aechatusers::model()->findByAttributes(array('context_key' => $this->context_key));

        if(!isset($obj->chat_id)){
            return false;
        }

        /* get all users of the current chat and move them to the new chat */
        $users = Aechatusers::model()->findAllByAttributes(array('context_key' => $this->context_key));

        /* owner of the group chat will be who ever creates it */
        $this->createNewGroupChat($this->current_playid,'group',false);

        foreach ($users as $user){
            $id = $user->chat_user_play_id;
            $this->addUserToChat($this->chat_id,$id,$this->context_key);
        }

        /* the newly added users */
        $this->addUserToChat($this->chat_id,$adduser,$this->context_key);

        /* important, updating the cached instance */
        $this->setCache($this->context_key);

    }

    public function createNewGroupChat($playid,$type='group',$adduser=true){
        $chat = new Aechat();
        $chat->owner_play_id = $playid;
        $chat->type = $type;
        $chat->game_id = $this->current_gid;
        $chat->insert();

        $this->chat_id = $chat->id;
        $this->context_key = 'groupchat-' .Helper::generateTokenKey();

        $this->setCache($this->context_key);

        /* add user to chat */
        if($adduser){
            $this->addUserToChat($this->chat_id,$playid,$this->context_key);
        }

    }

    public function showParticipants(){

        $users = Aechatusers::model()->findAllByAttributes(array('context_key' => $this->context_key));
        $p_users = array();

        foreach ($users as $user){
            $id = $user->chat_user_play_id;
            $vars = AeplayVariable::getArrayOfPlayvariables($id);
            $vars['id'] = $id;
            $p_users[] = $vars;
            $addedusers[$id] = true;
        }
        
        if ( isset($addedusers) ) {
            $this->participant_count = count($addedusers);
        } else {
            return false;
        }

        $this->getInviteUsers( $addedusers );
        
        $this->data->scroll[] = $this->getHeading('{#participants#}');

        foreach ($p_users as $p_user){
            $this->data->scroll[] = $this->getMyMatchItem($p_user, $p_user['id']);
        }

    }

    public function getInviteUsers( $addedusers ) {
        $users = AeplayKeyvaluestorage::model()->findAllByAttributes(array('play_id' => $this->current_playid,'key' => 'two-way-matches'));

        if ( empty($users) ) {
            return false;
        }

        if (
            isset($addedusers) AND
            count($addedusers) == 2 AND
            !strstr($this->chat_obj->type,'group')
        ) {
            $this->data->scroll[] = $this->getHeading('{#invite#} ({#will_create_a_new_group_chat#})');
        } elseif (isset($this->chat_obj->id)) {
            $this->data->scroll[] = $this->getHeading('{#invite#}');
        }

        foreach ($users as $user){
            $id = $user->value;
            if(!isset($addedusers[$id])){
                $vars = AeplayVariable::getArrayOfPlayvariables($id);
                $this->data->scroll[] = $this->getMyMatchItem($vars,$id,true);
            }
        }
    }

    public function getFirstName($vars){

        if ( isset($vars['screen_name']) ) {
            return $vars['screen_name'];
        }

        if ( isset($vars['company']) ) {
            return $vars['company'];
        }

        if ( !isset($vars['real_name']) OR empty($vars['real_name']) ) {
            return false;
        }

        $name = $vars['real_name'];
        
        $firstname = explode(' ', trim($name));
        $firstname = ucfirst($firstname[0]);
        return $firstname;
    }

    public function getMyMatchItem($vars,$id,$invite=false){

        $name = $this->getFirstName( $vars );
        $name = isset($vars['city']) ? $name.', '.$vars['city'] : $name;

        $profiledescription = isset($vars['profile_comment']) ? $vars['profile_comment'] : '-';

        $imageparams['style'] = 'round_image_imate';
        $imageparams['priority'] = 9;

        $imageparams['onclick'] = new StdClass();
        $imageparams['onclick']->action = 'open-action';
        $imageparams['onclick']->id = $id;
        $imageparams['onclick']->back_button = true;
        $imageparams['onclick']->sync_open = true;
        $imageparams['onclick']->action_config = $this->requireConfigParam('detail_view');

        $textparams['style'] = 'imate_title';
        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';


        /* text next to image */
        $namecol[] = $this->getText($name, $textparams);
        if(isset($time)){
            $txt = ($time == '{#now#}') ? $time : $time .' ago';
            $namecol[] = $this->getText($txt,array('style' => 'imate_title_msgtime'));
        }

        $textrows[] = $this->getRow($namecol);
        $textparams['style'] = 'imate_title_subtext';

        if(strlen($profiledescription) > 35){
            $textrows[] = $this->getText(mb_substr($profiledescription,0,35).'...', $textparams);
        } else {
            $textrows[] = $this->getText($profiledescription, $textparams);
        }

        /* get the row */
        $columns[] = $this->getImage($profilepic, $imageparams);
        $columns[] = $this->getColumn( $textrows, array( 'width' => '60%', 'vertical-align' => 'middle' ) );

        /* invitations */
        if($invite){
            $add = new stdClass();

            if($this->participant_count == 2){
                $add->id = 'adduser_create_'.$id;
            } else {
                $add->id = 'adduser_'.$id;
            }

            $add->action = 'submit-form-content';
            $columns[] = $this->getImage('add-user-to-chat.png', array('margin' => '15 0 15 10','onclick' => $add));
        }
        
        $rowparams['margin'] = '0 10 5 10';
        $rowparams['vertical-align'] = 'middle';
        $rowparams['height'] = '65';

        $add = new stdClass();
        $add->id = 'kickuser_'.$id;
        $add->action = 'submit-form-content';

        if(isset($this->chat_obj->owner_play_id) AND $this->chat_obj->owner_play_id == $this->current_playid AND $id != $this->current_playid AND $invite==false){
            $columns[] = $this->getImage('kick-icon-chat.png',array('margin' => '15 0 15 10','onclick' => $add));
        }

        $final[] = $this->getRow($columns,$rowparams);
        $final[] = $this->getText('',array('style' => 'contacts_spacer'));

        return $this->getColumn($final);
    }

    public function getHeading( $heading ) {
        return $this->getSettingsTitle( $heading );   
    }

    public function getChatDivs() {
        return false;
    }

	public function attachParameters() {
    	// placeholder
	}

}