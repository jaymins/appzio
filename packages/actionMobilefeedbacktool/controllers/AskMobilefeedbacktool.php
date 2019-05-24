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

class AskMobilefeedbacktool extends MobilefeedbacktoolController {

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

    /* to myself / other */
    public function tab1(){
        $this->data = new stdClass();
        $this->generalInit();

        if(!empty($this->userlist['subordinates'])){
            $this->selectWho();
        } else {
            $this->data->scroll[] = $this->getShadowbox('{#select_who_should_give_feedback#}');
            $this->recipientList(true,4);
        }

        return $this->data;
    }

    /* to whom */
    public function tab2(){
        $this->data = new stdClass();
        $this->generalInit();

        if($this->current_tab == 2){
            $this->sessionSet('feedback_recipient',false);
        }

        $this->data->scroll[] = $this->getShadowbox('{#select_a_team_member_who_you_are_requesting_feedback_of#}');
        $my_department = $this->getSavedVariable('department');

        if(!empty($this->userlist['subordinates'])){
            $data = $this->userlist['subordinates'];
            $userdata = $this->filterUserData($data);
            $userdata[$my_department] = $userdata;
            $this->userlisting($userdata,3,'feedback_recipient');
        } else {
            $this->data->scroll[] = $this->getText('{#no_people_found#}',array('text-align' => 'center'));
        }

        return $this->data;
    }

    /* who should give feedback */
    public function tab3(){
        $this->data = new stdClass();

        $this->generalInit();
        $this->data->scroll[] = $this->getShadowbox('{#select_who_should_give_feedback#}');

        if($this->current_tab == 3 AND $this->sessionGet('feedback_recipient')){
            $this->rewriteAbout();
        }

        if($this->sessionGet('feedback_recipient')){
            $this->person_filtering[] = $this->sessionGet('feedback_recipient');
        }

        $this->recipientList(true,4);
        return $this->data;
    }


    private function rewriteAbout(){
        $about = $this->localizationComponent->smartLocalize('{#about#}');

        $recipient = $this->sessionGet('feedback_recipient');
        if(is_numeric($recipient) AND isset($this->userlist['names_by_id'][$recipient]['name'])){
            $recipient = $this->userlist['names_by_id'][$recipient]['name'];
        }

        $this->rewriteActionField('subject',$about .' ' .$recipient);

    }


    /* aka choose send a msg */
    public function tab4()
    {
        $this->data = new stdClass();
        if($this->current_tab == 4 AND $this->sessionGet('feedback_recipient')){
            $this->rewriteAbout();
        }

        $this->addFundamentals('{#please_choose_whether_feedback_should_be_related_to_some_fundamental#}',5);
        return $this->data;
    }


    /* aka choose send a msg */
    public function tab5()
    {
        $this->data = new stdClass();

        if($this->current_tab == 5 AND $this->sessionGet('feedback_recipient')){
            $this->rewriteAbout();
        }


        if($this->menuid == 'sendask'){
            $this->sendAsk();
            return $this->data;
        }

        $this->askFeedbackForm();

        return $this->data;
    }


    public function selectWho(){
        $this->data->scroll[] = $this->getShadowbox('{#select_who_you_are_requesting_feedback_to#}');
        $this->data->scroll[] = $this->selectionButton('{#requesting_feedback_for_myself#}',$this->playid,'feedback_recipient',3);
        $this->data->scroll[] = $this->selectionButton('{#requesting_feedback_to_my_team_member#}','employee','feedbackto',2);

    }


    public function askFeedbackForm(){
        $this->bottom_menu_json = false;
        $this->data->scroll[] = $this->getMsgHeader(false,'request');

        $fundamental = $this->getFundamentalNameById($this->sessionGet('fundamental'));

/*        if($fundamental){
            $this->data->scroll[] = $this->getText('Fundamental:' .$fundamental,array('text-align' => 'center'));
        }*/

        // $this->data->scroll[] = $this->formkitField('temp_subject','{#subject#}','{#enter_subject#}',false,false,'{#please_provide_feedback#}');
        $this->data->scroll[] = $this->formkitTextarea('temp_request_message','{#message#}','{#short_message_about_your_feedback_request#}');

        unset($col);
        $col[] = $this->getText('{#cancel#}',array('onclick' => $this->getOnclick('tab1'),
            'width' => '30%',
            'background-color' => $this->color_topbar,'border-radius' => '8',
            'padding' => '10 4 10 4','color' => '#ffffff','text-align' => 'center','text-size' => '14'));
        $col[] = $this->getVerticalSpacer('4%');
        $col[] = $this->getText('{#send#}',array('onclick' => $this->getOnclick('id',false,'sendask'),
            'width' => '30%',
            'background-color' => $this->color_topbar,'border-radius' => '8',
            'padding' => '10 4 10 4','color' => '#ffffff','text-align' => 'center','text-size' => '14'));
        $this->data->footer[] = $this->getRow($col,array('text-align' => 'center','margin' => '10 0 10 0'));
    }


    public function sendAsk() {
        $this->bottom_menu_json = false;

        $obj = new MobilefeedbacktoolModel();
        $obj->requester_id = $this->playid;
        $obj->game_id = $this->gid;
        $obj->playid = $this->playid;
        $obj->is_request = 1;

        if(is_numeric($this->sessionGet('feedback_recipient'))){
            $obj->recipient_id = $this->sessionGet('feedback_recipient');
        } else {
            $obj->pending_username = $this->sessionGet('feedback_recipient');
        }

        if(is_numeric($this->sessionGet('selected_person'))){
            $obj->author_id = $this->sessionGet('selected_person');
        } else {
            $obj->author_id = NULL;
            $obj->pending_author_username = $this->sessionGet('selected_person');
        }

        if(is_numeric($this->sessionGet('fundamental'))){
            $obj->fundamentals_id = $this->sessionGet('fundamental');
        }

        $obj->request_subject = $this->getSubmittedVariableByName('temp_subject');
        $obj->request_message = $this->getSubmittedVariableByName('temp_request_message');
        $obj->insert();

        $this->feedbackNotify($this->sessionGet('selected_person'),'feedback-request',false,false,'show_msg_'.$obj->id);

        $output[] = $this->getImage('msg-sent.png',array('margin' => '20 0 20 0','text-align' => 'center','height' => '120'));
        $output[] = $this->getText('+1pt',array('style' => 'places_toptext_grey'));
        Aeplay::addSubtractPoints($this->playid,'primary',1,$this->gid);
        $output[] = $this->getText('{#request_sent#}',array('style' => 'places_toptext_grey'));

        $this->sessionSet('feedback_recipient',false);
        $this->sessionSet('selected_person',false);

        $this->data->scroll[] = $this->getColumn($output,array('text-align' => 'center','width' => '100%'));
        $col[] = $this->getText('{#ok#}!',array('onclick' => $this->getOnclick('go-home',false),
            'width' => '30%',
            'background-color' => $this->color_topbar,'border-radius' => '8',
            'padding' => '10 4 10 4','color' => '#ffffff','text-align' => 'center','text-size' => '14'));
        $this->data->footer[] = $this->getRow($col,array('text-align' => 'center','margin' => '10 0 10 0'));
    }




}