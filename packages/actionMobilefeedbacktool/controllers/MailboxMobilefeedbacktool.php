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

class MailboxMobilefeedbacktool extends MobilefeedbacktoolController {

    public $data;

    public $configobj;
    public $theme;
    public $profileid;

    public $margin;
    public $grid;
    public $mode;
    public $cachename;

    public $deleting;
    public $points;
    public $secondary_points;

    /* @var MobilefeedbacktoolModel*/
    public $dataobj;
    public $msgcount;

    /* inbox includes the whole layout code, we call it on tab 1 to get the count */
    public $inbox;
    public $gifinfo;

    /* array of id's or names, that will be applied when listing people */
    public $person_filtering = array();


    /* aka unread */
    public function tab1(){
        $this->data = new stdClass();

        if($this->current_tab == '1'){
            $subject = $this->localizationComponent->smartLocalize('{#unread_message#}');
            $this->rewriteActionField('subject',$subject);
            $this->rewriteActionConfigField('pull_to_refresh',1);
        }

        /* this is for incoming push messages */
        if(stristr($this->menuid,'show_msg_')){
            $id = str_replace('show_msg_','',$this->menuid);
            if(!$this->sessionGet($this->menuid)){
                $this->sessionSet($this->menuid,1);
                $this->sessionSet('open-msg',$id);

                $onclick = new stdClass();
                $onclick->id = $id;
                $onclick->action = 'open-tab';
                $onclick->action_config = 2;
                $onclick->back_button = 1;
                $onclick->sync_open = 1;
                $this->data->onload[] = $onclick;

                return $this->data;
            }
        }

        /* as there is also action refresh, we only open it on the second one */
/*        if($this->sessionGet('open-msg')){
            $id = $this->sessionGet('open-msg');
            $onclick = new stdClass();
            $onclick->id = $id;
            $onclick->action = 'open-tab';
            $onclick->action_config = 2;
            $onclick->back_button = 1;
            $onclick->sync_open = 1;
            $this->sessionSet('open-msg',false);
            $this->data->onload[] = $onclick;
        }*/

        $this->generalInit();
        $this->tabHeader(1);
        $this->msgSearch();
        $this->viewMsgList('unread_all');

        $this->data->footer[] = $this->getText('{#feedbacks_are_marked_read_only_after_your_response#}',array('text-align' => 'center','margin' => '7 0 7 0','font-size' => '14'));

        return $this->data;
    }

    /* aka message */
    public function tab2(){
        $this->data = new stdClass();
        $this->generalInit();

        if($this->current_tab == '2'){
            $this->rewriteActionConfigField('pull_to_refresh',0);
        }

        if(strstr($this->menuid,'mark_read_')){
            $id = str_replace('mark_read_','',$this->menuid);
            $obj = MobilefeedbacktoolModel::model()->findByPk($id);
            if(is_object($obj)){
                $obj->msg_read = 1;
                $obj->update();
            }
            $this->no_output = true;
            return $this->data;
        }


        if(strstr($this->menuid,'archive_message_')){
            $id = str_replace('archive_message_','',$this->menuid);

            if(strstr($id,'un_')){
                $id = str_replace('un_','',$id);
                $this->archiveMsg($id,true);
            } else {
                $this->archiveMsg($id,false);
            }

            $this->no_output = true;
            return $this->data;
        }

        if($this->menuid == 'submit_response'){
            $this->sendResponse();
        } else {
            $this->viewMsg();
        }


        return $this->data;

    }


    /* aka received */
    public function tab3(){
        $this->data = new stdClass();

        if($this->current_tab == '3'){
            $subject = $this->localizationComponent->smartLocalize('{#received_messages#}');
            $this->rewriteActionField('subject',$subject);
            $this->rewriteActionConfigField('pull_to_refresh',1);
        }

        $this->generalInit();
        $this->tabHeader(3);
        $this->msgSearch();
        $this->viewMsgList('inbox');
        return $this->data;
    }


    /* aka sent */
    public function tab4(){
        $this->data = new stdClass();

        if($this->current_tab == '4'){
            $subject = $this->localizationComponent->smartLocalize('{#sent_messages#}');
            $this->rewriteActionField('subject',$subject);
            $this->rewriteActionConfigField('pull_to_refresh',1);
        }

        $this->generalInit();
        $this->tabHeader(4);
        $this->msgSearch();
        $this->viewMsgList('outbox');
        return $this->data;
    }

    /* aka archive */
    public function tab5(){
        $this->data = new stdClass();

        if($this->current_tab == '5'){
            $subject = $this->localizationComponent->smartLocalize('{#archive#}');
            $this->rewriteActionField('subject',$subject);
            $this->rewriteActionConfigField('pull_to_refresh',1);
        }

        $this->generalInit();
        $this->tabHeader(5);
        $this->msgSearch();
        $this->viewMsgList('archived');
        return $this->data;
    }


    public function tabHeader($tab){
        $this->data->header[] = $this->getTabs(array('tab1' => '{#unread#}','tab3'=>'{#received#}','tab4' => '{#sent#}','tab5' => '{#archive#}'),false,false,$tab);
    }

    public function archiveMsg($id,$unarchive=false){
        $obj = MobilefeedbacktoolModel::model()->findByPk($id);

        if(!is_object($obj)){
            return false;
        }

        if($unarchive){
            $flag = 0;
        } else {
            $flag = 1;
        }

        if($obj->recipient_id == $this->playid AND $obj->is_request == 0){
            $obj->msg_archived = $flag;
        } elseif($obj->is_request == 0) {
            $obj->comment_archived = $flag;
        } elseif($obj->recipient_id == $this->playid AND $obj->is_request == 1){
            $obj->msg_archived = $flag;
        } elseif($obj->requester_id == $this->playid AND $obj->is_request == 1) {
            $obj->msg_archived = $flag;
        } else {
            $obj->comment_archived = $flag;
        }

        $obj->update();
        return true;

    }


    public function msgSearch(){
        if($this->menuid == 'close-search'){
            unset($this->submitvariables['searchterm']);
            $term = false;
        } else {
            $term = $this->getSubmittedVariableByName('searchterm');
            $term = trim($term);
        }

        $row[] = $this->getImage('search-icon-for-field.png',array('height' => '25'));
        $row[] = $this->getFieldtext($term,array('style' => 'example_searchbox_text',
            'hint' => '{#search_all_messages#}','variable' => 'searchterm',
            //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
            'id' => 'something',
            'submit_menu_id' => 'custom_card_search',
            'suggestions_style_row' => 'example_list_row','suggestions_text_style' => 'example_list_text',
            //'submit_on_entry' => '1','activation' => 'initially'
        ));

        if(isset($this->submitvariables['searchterm']) AND $this->submitvariables['searchterm']){
            //$this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));
            $cc = new stdClass();
            $cc->id = 'close-search';
            $cc->action = 'submit-form-content';
            $row[] = $this->getImage('nice-cancel-icon.png',array('width' => '25','onclick' => $cc,'margin' => '1 0 0 0','floating' => '1', 'float' => 'right'));
        }

        $col[] = $this->getVerticalSpacer('10');
        $col[] = $this->getRow($row,array('style' => 'example_searchbox','width' => '70%'));

        $col[] = $this->getTextbutton('Search',array('style' => 'example_searchbtn','id' => 'custom_card_search',
        ));
        $this->data->scroll[] = $this->getRow($col,array('background-color' => $this->color_topbar));

    }


    /* aka msg */
    public function viewMsg(){
        $this->data = new stdClass();

        if($this->current_tab != 2){
            $this->data->scroll[] = $this->getFullPageLoader();
            return $this->data;
        }

        $id = $this->menuid;
        $this->sessionSet('current_message',$id);
        $obj = MobilefeedbacktoolModel::model()->findByPk($id);

        if(!is_object($obj)){
            $this->data->scroll[] = $this->getText('{#msg_not_found#}',array('style' => 'settings_title'));
            $this->data->scroll[] = $this->getText('{#it_might_be_deleted#}',array('style' => 'settings_title'));
            $this->data->footer[] = $this->getTextbutton('{#back#}', array('id' => 'cancel', 'onclick' => $this->getOnclick('id',false,'cancel')));
            return $this->data;
        }

        if($obj->is_request == 1){
            $this->viewRequestBody($obj,$id);
        } else {
            $this->viewMsgBody($obj,$id);
        }
    }


    public function viewMsgBody($obj,$id,$inside_request=false){
        /* mark comment as read */
        if($obj->author_id == $this->playid AND $obj->feedback_rating AND !$obj->comment_read){
            $obj->comment_read = 1;
            $obj->update();

            /* will update the unread badge on the messages */
            Aenotification::addUserNotification( $this->playid, false, false, '-1', $this->gid );
        }

        if($inside_request){

        } else {
            $this->data->scroll[] = $this->getMsgHeader($id,false,$obj);
        }

        if($obj->pic){
            $this->data->scroll[] = $this->getImage($obj->pic);
            $this->data->scroll[] = $this->getSpacer(10);
        }

        if($obj->subject AND $this->current_tab == '2'){
            $this->rewriteActionField('subject',strtoupper($obj->subject));
        }

        $this->data->scroll[] = $this->getText($obj->message,array('margin' => '10 10 0 10','color' => '#000000','font-size' => '13'));

        $this->data->scroll[] = $this->formkitTitle('{#what_was_excellent#}');
        $this->data->scroll[] = $this->getText($obj->excellent,array('font-size' => '13','color' => '#BABABA','margin' => '0 10 10 10'));

        $this->data->scroll[] = $this->formkitTitle('{#what_to_maintain#}');
        $this->data->scroll[] = $this->getText($obj->to_maintain,array('font-size' => '13','color' => '#BABABA','margin' => '0 10 10 10'));

        $this->data->scroll[] = $this->formkitTitle('{#what_to_change#}');
        $this->data->scroll[] = $this->getText($obj->to_change,array('font-size' => '13','color' => '#BABABA','margin' => '0 10 10 10'));

        if(!$obj->feedback_rating AND !$obj->comment AND $obj->author_id != $this->playid){
            $this->data->scroll[] = $this->formkitTitle('{#my_response_to_feedback#}');

            $this->data->scroll[] = $this->getFieldtextarea('',array('variable' => 'temp_response',
                'hint' => '{#my_response_to_feedback#} ({#not_mandatory#})','margin' => '0 10 10 8','font-size' => '13','color' => '#000000',));
        }

        $this->data->scroll[] = $this->getSpacer(10);

        $commenter = $obj->requester_id ? $obj->requester_id : $obj->recipient_id;


        if(isset($this->userlist['names_by_id'][$commenter]['name'])){
            $commenter = '({#by#} ' .$this->userlist['names_by_id'][$commenter]['name'] .')';
        } else {
            $commenter = '';
        }

        if(strlen($obj->comment) > 400){
            $part = 'scroll';
        } else {
            $part = 'footer';
        }

        /* comment for testing */

        if(!$obj->feedback_rating AND $obj->author_id != $this->playid){
            $this->happinessIndicator('{#feedback_usefulness#}');
/*            $this->data->footer[] = $this->getText('',array('margin' => '0 0 5 0','height' => '1','opacity' => '0.4','background-color' => '#BABABA'));
            $this->data->footer[] = $this->getSpacer(10);
            $this->data->footer[] = $this->formkitSlider('{#rate_feedback#} (1 = {#not_helpful#}, 10 = {#super_helpful#}','temp_happiness','5','1','10','1');*/
        } elseif($obj->comment) {
            $this->data->$part[] = $this->getText('',array('height' => 4,'background-color' => $this->color_topbar));
            $this->data->$part[] = $this->formkitTitle('{#response_to_feedback#} '.$commenter);
            $this->data->$part[] = $this->getText($obj->comment,array('margin' => '4 10 0 10','color' => '#000000','font-size' => '13'));
            $this->data->$part[] = $this->getSpacer(15);
        } elseif($obj->feedback_rating) {
            $this->data->$part[] = $this->formkitTitle('{#this_feedback_has_been_rated#} '.$commenter);
        }

        $this->data->scroll[] = $this->getSpacer(20);

        if(!$obj->feedback_rating AND $obj->author_id != $this->playid) {
            $buttons[] = array('title' => '{#back#}','onclick' => $this->getOnclick('tab1'));
            $buttons[] = array('title' => '{#respond#}','onclick' => $this->getOnclick('id',false,'submit_response'));
            $this->getFeedbackToolButtons($buttons,'scroll');
        } elseif($obj->msg_archived == 0 AND $obj->author_id != $this->playid) {
            $onclick[] =$this->getOnclick('id',false,'archive_message_'.$id);
            $onclick[] = $this->getOnclick('tab1');
            $buttons[] = array('title' => '{#archive_message#}','onclick' => $onclick);
            $this->getFeedbackToolButtons($buttons,'scroll');
        } elseif($obj->msg_archived == 1) {
            $onclick[] =$this->getOnclick('id',false,'un_archive_message_'.$id);
            $onclick[] = $this->getOnclick('tab4');
            $buttons[] = array('title' => '{#unarchive#}','onclick' => $onclick);
            $this->getFeedbackToolButtons($buttons,'scroll');
        } elseif($obj->request_comment_read AND $obj->comment_archived == 0){
            $onclick[] =$this->getOnclick('id',false,'archive_message_'.$id);
            $onclick[] = $this->getOnclick('tab1');
            $buttons[] = array('title' => '{#archive_message#}','onclick' => $onclick);
            $this->getFeedbackToolButtons($buttons,'footer');
        } elseif($obj->request_comment_read AND $obj->comment_archived == 1){
            $onclick[] =$this->getOnclick('id',false,'un_archive_message_'.$id);
            $onclick[] = $this->getOnclick('tab4');
            $buttons[] = array('title' => '{#unarchive#}','onclick' => $onclick);
            $this->getFeedbackToolButtons($buttons,'footer');

        }
    }


    public function viewRequestBody($obj,$id){
        /* mark comment as read */
        if($obj->author_id == $this->playid AND $obj->feedback_rating AND !$obj->comment_read){
            $obj->comment_read = 1;
            $obj->update();
        }

        if($obj->author_id == $this->playid AND $obj->feedback_rating AND !$obj->request_comment_read) {
            $obj->request_comment_read = 1;
            $obj->update();

        }

        if($obj->pic){
            $this->data->scroll[] = $this->getImage($obj->pic);
            $this->data->scroll[] = $this->getSpacer(10);
        }

        $this->data->scroll[] = $this->getMsgHeader($id,'request',$obj);
        $this->data->scroll[] = $this->getText($obj->request_message,array('margin' => '10 10 0 10','color' => '#000000','font-size' => '13'));

        $cliker = new stdClass();
        $cliker->action = 'open-action';
        $cliker->action_config = $this->getActionidByPermaname('sender');
        $cliker->id = 'feedbackfromid_'.$id;
        $cliker->sync_open = 1;

        $mark_read = new stdClass();
        $mark_read->action = 'submit-form-content';
        $mark_read->id = 'mark_read_'.$id;

        if($obj->rating){
            $this->viewMsgBody($obj,$id,true);
        } else {
            if($this->playid != $obj->requester_id){
                $buttons[] = array('title' => '{#later#}','onclick' => array($mark_read,$this->getOnclick('tab1')));
                $buttons[] = array('title' => '{#decline#}','onclick' => array($mark_read,$this->getOnclick('tab1')));
                $buttons[] = array('title' => '{#give_feedback#}','onclick' => array($mark_read,$cliker));

                $this->getFeedbackToolButtons($buttons);
            } else {
                $this->data->footer[] = $this->getText('{#the_feedback_will_be_shown_here_once_its_given#}',array('text-align' => 'center','margin' => '10 10 10 10','color' => '#000000','font-size' => '13'));
            }
        }

    }





    public function sendResponse(){

        $id = $this->sessionGet('current_message');
        $rating = $this->getSubmittedVariableByName('temp_happiness');
        $response = $this->getSubmittedVariableByName('temp_response');
        $obj = MobilefeedbacktoolModel::model()->findByPk($id);

        if(!is_object($obj)){
            $this->data->footer[] = $this->getText('{#there_was_an_unknown_error#}',array('margin' => '0 0 5 0','height' => '1','opacity' => '0.4','background-color' => '#BABABA'));
            return true;
        }

        if($rating <= 5){
            Aeplay::addSubtractPoints($obj->author_id,'primary','-1',$this->gid);
        } elseif($rating > 5 AND $rating < 9) {
            Aeplay::addSubtractPoints($obj->author_id,'primary','1',$this->gid);
        } else {
            Aeplay::addSubtractPoints($obj->author_id,'primary','2',$this->gid);
        }

        Aeplay::addSubtractPoints($this->playid,'primary',2,$this->gid);

        $obj->feedback_rating = $rating;
        $obj->comment = $response;
        $obj->msg_read = 1;
        $obj->update();
        
        $this->data->scroll[] = $this->getImage('thumbup.png',array('margin' => '40 40 0 40'));
        $this->data->onload[] = $this->getOnclick('list-branches');
        $btn[] = array('title' => '{#ok#}!','onclick' => $this->getOnclick('tab1'));

        $this->feedbackNotify($obj->author_id,'feedback-response',false,false,'show_msg_'.$obj->id);
        $this->getFeedbackToolButtons($btn);

    }


    /* aka msg list */
    public function viewMsgList($mode='inbox'){

        if(isset($this->submitvariables['searchterm']) AND $this->submitvariables['searchterm']){
            $cc = new stdClass();
            $cc->id = 'close-search';
            $cc->action = 'submit-form-content';

            $this->data->scroll[] = $this->getText('{#displaying_search_results#}',array('onclick'=> $cc,
                'background-color' => $this->color_topbar_hilite,'color' => '#ffffff','padding' => '12 7 12 7','text-align' => 'center','font-size' => '13'
            ));
            $this->data->scroll[] = $this->getSpacer(10);


            $this->getMsgListing('search');

        } else {
            $this->data->scroll[] = $this->getSpacer(10);
            $this->getMsgListing($mode);
        }


    }


    public function getMsgListing($context){

        if(isset($this->msglist[$context])){
            $data = $this->msglist[$context];
        } else {
            $data = $this->msglist['all_messages'];
        }

        foreach($data as $msg){

            $profilepic = basename($msg['otheruser_pic']);
            $name = $msg['otheruser_name'];

            if(!$profilepic OR !$this->imagesobj->findFile($profilepic)){
                $profilepic = 'profile-image-placeholder.png';
            }

            if(isset($this->submitvariables['searchterm']) AND $this->submitvariables['searchterm']){
                if(is_array($msg)){
                    $heystack = $name .implode(' ',$msg);

                    if(!stristr($heystack,$this->submitvariables['searchterm'])){
                        continue;
                    }
                }
            }

            $this->msgListItem($msg,$profilepic,$name,$context);
        }

        if(empty($data)){
            $this->data->scroll[] = $this->getText('{#no_messages_yet#}',array('text-align' => 'center','margin' => '10 0 0 0','font-size' => '14'));
        }

    }


    public function msgListItem($msg,$profilepic,$name,$context){

        $col[] = $this->getImage($profilepic,array('width' => '60','crop' => 'round','vertical-align' => 'middle','margin' => '0 15 0 0',
            'imgwidth' => '120','imgheight' => '120'));

        if($msg['is_request']){
            /* about whom */
            $people = $this->getPeopleRequest((object)$msg);

            if($people['about']){
                $subject_name = '{#about#}: '.$people['about'];
            } elseif($people['to'] != $this->getSavedVariable('real_name') AND $people['to']) {
                $subject_name = '{#to#}: '.$people['to'];
            } elseif($people['requester'] != $this->getSavedVariable('real_name') AND $people['requester']) {
                $subject_name = '{#requester#}: '.$people['requester'];
            } elseif(isset($people['pending_author_username']) AND $people['pending_author_username'] != $this->getSavedVariable('real_name') AND $people['pending_author_username']) {
                $subject_name = $people['pending_author_username'];
            } else {
                $subject_name = '';
            }

            $title = strtoupper($this->localizationComponent->smartLocalize('{#request#}'));
            $row[] = $this->getText($title,array('font-size' => '12','color' => '#BABABA'));
            $row[] = $this->getText($subject_name,array('font-size' => '14'));

            $row[] = $this->getText($msg['request_subject'],array('font-size' => '12','color' => '#BABABA'));
            $row[] = $this->getText(mb_substr($msg['request_message'],0,50),array('font-size' => '12','color' => '#BABABA'));
        } else {
            $title = strtoupper($this->localizationComponent->smartLocalize('{#feedback#}'));
            $row[] = $this->getText($title,array('font-size' => '12','color' => '#BABABA'));
            $row[] = $this->getText($name,array('font-size' => '14'));
            $row[] = $this->getText($msg['subject'],array('font-size' => '12','color' => '#BABABA'));
        }

        $col[] = $this->getColumn($row,array('vertical-align' => 'top','width' => '150'));
        $date = strtotime($msg['date']);
        /* adjustment for production server*/
        $date = $date-7200;
        $lastcol[] = $this->getText(Controller::humanTiming($date),array('font-size' => '12','color' => '#BABABA','text-align' => 'right','vertical-align' => 'top'));

        if($msg['flag']){
            $lastcol[] = $this->getImage('red-dot.png',array('width' => '15','margin' => '0 0 0 10'));
        }


        if(!$msg['comment_read'] AND $context == 'outbox' AND $msg['comment']){
            $lastcol[] = $this->getImage('red-dot.png',array('width' => '15','margin' => '0 0 0 10'));
            if(isset($msg['id'])){
                $ob = MobilefeedbacktoolModel::model()->findByPk($msg['id']);
                $ob->comment_read = 1;
                $ob->update();
            }
        }

        $col[] = $this->getRow($lastcol,array());

        if($msg['is_request']){
            $onclick = $this->getOnclickTabAndSave('msgid',$msg['id'],2,true);
        } else {
            $onclick = $this->getOnclickTabAndSave('requestid',$msg['id'],2,true);
        }

        $onclick = new stdClass();
        $onclick->id = $msg['id'];
        $onclick->action = 'open-tab';
        $onclick->action_config = 2;
        $onclick->back_button = 1;
        $onclick->sync_open = 1;
        $onclick->context = 'msg-'.$msg['id'];

        $this->data->scroll[] = $this->getRow($col,array('margin' => '0 20 0 20','vertical-align' => 'top','onclick' => $onclick));
        $this->data->scroll[] = $this->getText('',array('margin' => '10 20 10 20','height' => '1','opacity' => '0.4','background-color' => '#BABABA'));

        unset($row);
        unset($col);
        unset($lastcol);

    }



}