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

class SendingMobilefeedbacktool extends MobilefeedbacktoolController {

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
    public $fundamentals;

    /* aka search for gif */
    public function tab1(){
        $this->data = new stdClass();
        $this->generalInit();

        if($this->menuid == 'remove-gif'){
            $this->sessionSet('gif_url','');
            $this->no_output = true;
            return $this->data;
        }

        if(strstr($this->menuid,'sendfeedback_')){
            $id = str_replace('sendfeedback_','',$this->menuid);
            $this->sendFeedback($id);
            return $this->data;
        }

        if($this->menuid == 'cancel'){
            $this->sessionSet('selected_mode',false);
            unset($this->session_storage['selected_mode']);

            $this->sessionSet('gif_url',false);
            unset($this->session_storage['gif_url']);

            $this->sessionSet('selected_person',false);
            unset($this->session_storage['selected_person']);
        }

        if(strstr($this->menuid,'feedbackfromid_')){
            $id = str_replace('feedbackfromid_','',$this->menuid);
            $this->newFeedbackForm($id);
            return $this->data;
        }

        if(strstr($this->menuid,'gif_selected_') OR $this->sessionGet('selected_mode')){
            if(strstr($this->menuid,'gif_selected_')){
                $gif = str_replace('gif_selected_','',$this->menuid);
                $this->sessionSet('gif_url',$gif);
            }
            
            $this->sessionSet('selected_mode',true);
            $this->data->scroll[] = $this->getShadowbox('{#choose_who_are_you_sending_this_feedback_to#}');
            $this->recipientList(true,$this->includeFundamentalsOrNot());
            $onclick = $this->getOnclick('id',false,'cancel');
            $btns[] = array('title' => '{#cancel#}','onclick' => $onclick);
            $this->data->footer[] = $this->getFeedbackToolButtons($btns);
            return $this->data;
        }

        $this->sessionSet('selected_mode',false);
        $this->customCard();
        return $this->data;
    }

    public function includeFundamentalsOrNot(){
        if($this->getSavedVariable('gifit_team_id')){

            $fundamentals = MobilefeedbacktoolfundamentalsModel::model()->findAllByAttributes(array('game_id' => $this->gid,'team_id' => $this->getSavedVariable('gifit_team_id')), array('order' => 'title'));

            if($this->getConfigParam('include_fundamentals') AND !empty($fundamentals)){
                $tab = 3;
            } else {
                $tab = 4;
            }

        } else {
            $tab = $this->getConfigParam('include_fundamentals') ? 3 : 4;
        }

        return $tab;
    }

    /* aka search for recipient */
    public function tab2(){
        $this->data = new stdClass();
        $this->data->scroll[] = $this->getShadowbox('{#choose_who_are_you_sending_this_feedback_to#}');
        $this->recipientList(true,$this->includeFundamentalsOrNot());
        return $this->data;
    }

    /* aka choose fundamentals */
    public function tab3(){
        $this->data = new stdClass();
        $this->addFundamentals();
        return $this->data;
    }

    /* aka choose send a msg */
    public function tab4()
    {
        $this->data = new stdClass();
        if($this->menuid == 'sendfeedback'){
            $this->sendFeedback();
            return $this->data;
        }

        $this->getSavedVariable('real_name');
        $this->newFeedbackForm();
        return $this->data;
    }


    public function customCard(){
        $this->data = new stdClass();

        $row[] = $this->getImage('search-icon-for-field.png',array('height' => '25'));
        $row[] = $this->getFieldtext($this->getSubmittedVariableByName('searchterm'),array('style' => 'example_searchbox_text',
            'hint' => '{#search_for_an_image#}','variable' => 'searchterm',
            //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
            'id' => 'something',
            'submit_menu_id' => 'custom_card_search',
            'suggestions_style_row' => 'example_list_row','suggestions_text_style' => 'example_list_text',
            //'submit_on_entry' => '1','activation' => 'initially'
        ));
        $col[] = $this->getVerticalSpacer('10');
        $col[] = $this->getRow($row,array('style' => 'example_searchbox','width' => '70%'));
        $col[] = $this->getTextbutton('Search',array('style' => 'example_searchbtn','id' => 'custom_card_search',
        ));
        $this->data->scroll[] = $this->getRow($col,array('background-color' => $this->color_topbar,'vertical-align' => 'middle'));

        if($this->menuid == 'custom_card_search'){
            $term = $this->getSubmittedVariableByName('searchterm');

            $results = ThirdpartyServices::giphySearch($term);
            $urls = $this->getGifUrls($results);
            $count = 0;

            foreach($urls as $gif){
                $count++;
                $this->data->scroll[] = $this->getGif($gif,false,false,true);
            }

            if($count == 0){
                $this->data->scroll[] = $this->getSpacer('20');
                $this->data->scroll[] = $this->getText('{#sorry_no_results_try_another_keyword#}',array('style' => 'places_toptext_grey'));
                $this->searchHinting();
            }

        } else {
            $this->searchHinting();
        }

        return $this->data;
    }

    public function searchHinting(){
        $this->data->scroll[] = $this->getSpacer('20');
        unset($col);
        unset($row);

        $col[] = $this->getImage('hint-arrow-up.png',array('margin' => '0 0 0 0'));
        $row[] = $this->getImage('powered-by-giphy-3.png',array('margin' => '44 0 0 0'));
        $row[] = $this->getText('{#note_that_search_can_take_a_little_while#}',array('font-size' => '13','color' => '#6B6B6B','margin' => '0 0 0 0'));
        $col[] = $this->getColumn($row,array('margin' => '0 20 0 0'));
        $this->data->scroll[] = $this->getRow($col);

        $onclick[] = $this->getOnclick('id',false,'remove-gif');
        $onclick[] = $this->getOnclick('tab2');
        $this->data->footer[] = $this->getButtonWithIcon('big-mail-icon.png','send-without-gif','{#send_feedback_without_a_gif#}',false,false,$onclick);

    }


    public function newFeedbackForm($id=false,$error=false){
        $this->bottom_menu_json = false;
        $about = false;

        if($id){
            $obj = MobilefeedbacktoolModel::model()->findByPk($id);
            if(!is_object($obj)){
                $this->data->scroll[] = $this->getText('There has been an unknown error');
                return true;
            } else {
                if(is_numeric($obj->requester_id) AND isset($this->userlist['names_by_id'][$obj->requester_id]['name'])){
                    $name = $this->userlist['names_by_id'][$obj->requester_id]['name'];
                } else {
                    $name = '{#unknown#}';
                }

                if(is_numeric($obj->recipient_id) AND isset($this->userlist['names_by_id'][$obj->recipient_id]['name'])){
                    $about = $this->userlist['names_by_id'][$obj->recipient_id]['name'];
                } elseif($obj->pending_username) {
                    $about = $obj->pending_username;
                }
            }
        } else {
            $person = $this->sessionGet('selected_person');
            if(is_numeric($person) AND isset($this->userlist['names_by_id'][$person]['name'])){
                $name = $this->userlist['names_by_id'][$person]['name'];
            } else {
                $name = $person;
            }
        }

        if($this->sessionGet('gif_url')){
            $url = $this->sessionGet('gif_url');
            $this->data->scroll[] = $this->getGifImageFile($url);
        }

        if($this->sessionGet('fundamental')){
          //  $this->data->scroll[] = $this->sessionGet('fundamental');
        }

        if($about){
            $col[] = $this->getText('{#from#}: '.$this->getSavedVariable('real_name') .', {#to#}:' .$name .', {#concerning#}: '.$about,array('style' => 'settings_title'));
        } else {
            $col[] = $this->getText('{#from#}: '.$this->getSavedVariable('real_name') .', {#to#}:' .$name,array('style' => 'settings_title'));
        }

        $this->data->scroll[] = $this->getColumn($col,array('style' => 'general_shadowbox'));
        $this->data->scroll[] = $this->formkitField('temp_subject','{#subject#}','{#enter_subject#}');
        $this->data->scroll[] = $this->formkitTextarea('temp_excellent','{#excellent#}','{#what_has_been_excellent#}');
        $this->data->scroll[] = $this->formkitTextarea('temp_maintain','{#maintain#}','{#what_should_be_maintained#}');
        $this->data->scroll[] = $this->formkitTextarea('temp_change','{#try_to_change#}','{#describe_what_should_be_changed#}');

        if($error){
            $this->data->footer[] = $this->getText($error,array('text-align'=> 'center','padding' => '10 10 10 10','background-color' => '#CB1601','color' => '#ffffff'));
        }
        
        $this->happinessIndicator('{#overall_rating#}');
        unset($col);

        //#} (1 = {#not_happy#}, 10 = {#super_happy#})','temp_happiness','5','1','10','1');


        $btn = new stdClass();
        $btn->action = 'open-tab';
        $btn->action_config = 'tab1';
        $btn->id = 'cancel';
        $btn->sync_open = 1;

        $col[] = $this->getText('{#cancel#}',array('onclick' => $btn,
            'width' => '30%',
            'background-color' => $this->color_topbar,'border-radius' => '8',
            'padding' => '10 4 10 4','color' => '#ffffff','text-align' => 'center','text-size' => '14'));
        $col[] = $this->getVerticalSpacer('4%');

        if($id){
            $onclick = $this->getOnclick('id',false,'sendfeedback_'.$id);
        } else {
            $onclick = $this->getOnclick('id',false,'sendfeedback');
        }

        $col[] = $this->getText('{#send#}',array('onclick' => $onclick,
            'width' => '30%',
            'background-color' => $this->color_topbar,'border-radius' => '8',
            'padding' => '10 4 10 4','color' => '#ffffff','text-align' => 'center','text-size' => '14'));

        $this->data->footer[] = $this->getRow($col,array('text-align' => 'center','margin' => '10 0 10 0'));
    }


    public function getGif($url,$count=false,$totalcount=false,$onpage=false){

        $filename = Controller::copyDirectlyToImagesFolder($this->gid,md5($url).'.gif',$url);
        $imagesfolder = Controller::getDomain($this->gid) .'/documents/games/' .$this->gid .'/images/';
        $onclick = $this->getOnclickTabAndSave('gif_url',$filename,2);


        if($onpage){
            $imagewidth = $this->screen_width;
        } else {
            $imagewidth = $this->screen_width-24;
        }

        if(isset($this->gifinfo[$url])){
            $ratio = $this->gifinfo[$url]['width'] / $this->gifinfo[$url]['height'];
            $height = $imagewidth/$ratio;
        } else {
            $height = $this->screen_width/1.3;
        }

        $imgs[] = $this->getImage($imagesfolder.$filename,array('use_filename' => 1,'width' => $imagewidth,'height' => $height));
        $page[] = $this->getColumn($imgs,array());

        if($count == false){
            return $this->getColumn($page,array('onclick' => $onclick));
        }

        $btn_width = $this->screen_width/2 - 10 - 12;

        $col[] = $this->getText('{#send_custom_feedback#}',array(
            'background-color' => '#ffffff','padding' => '10 10 10 10','border-radius' => '4','text-align' => 'center',
            'border-color' => '#e2e2e2',
            'onclick' => $onclick,
            //'onclick' => $this->getOnclick('id',true,'custom_card'),
            'font-size' => '13','width' => $btn_width));

        $col[] = $this->getVerticalSpacer('10');
        $col[] = $this->getText('{#send_this_card#}',array('background-color' => '#5edd84','padding' => '5 5 5 5','border-radius' => '4','text-align' => 'center',
            'font-size' => '13','width' =>$btn_width,'onclick' => $onclick,'color' => '#ffffff'));

        $page[] = $this->getRow($col,array('text-align' => 'center','padding' => '0 0 0 0','margin' => '4 0 6 0'));

        $swiper[] = $this->getColumn($page);

        if($onpage){
            $margin = '0 0 0 0';
        } else {
            $margin = '4 12 5 12';
        }

        $out[]=  $this->getColumn($swiper,array(
            'background-color' => '#ffffff','vertical-align' => 'bottom',
            'text-align' => 'center',
            'border-radius' => '4',
            'shadow-color' => '#66000000',
            'shadow-radius' => '2','shadow-offset' => '0 0',
            'margin' => $margin,'border-width' => '5','border-color' => '#ffffff'
        ));

        $out[] = $this->getSwipeNavi($totalcount,$count,array('navicolor' => 'black'));
        return $this->getColumn($out);
    }


    public function sendFeedback($id=false){

        if(!$this->getSubmittedVariableByName('temp_subject')){
            $this->newFeedbackForm(false,'{#at_least_subject_should_be_filled#}');
            return true;
        }

        if($id){
            $obj = MobilefeedbacktoolModel::model()->findByPk($id);
        } else {
            $obj = new MobilefeedbacktoolModel();
            $obj->author_id = $this->playid;
            $obj->game_id = $this->gid;
            $obj->playid = $this->playid;
            $person = $this->sessionGet('selected_person');

            if(is_numeric($person)){
                $obj->recipient_id = $person;
                $obj->department_id_recipient = $this->userlist['names_by_id'][$obj->recipient_id]['department_id'];
            } elseif(isset($this->userlist['names'][$person]['department_id'])) {
                $obj->pending_username = $person;
                $obj->department_id_recipient = $this->userlist['names'][$person]['department_id'];
            }

            if($this->sessionGet('fundamental')){
                $obj->fundamentals_id = $this->sessionGet('fundamental');
            }

            $obj->pic = $this->sessionGet('gif_url');
        }

        if(isset($this->userlist['names_by_id'][$this->playid]['department_id'])){
            $obj->department_id_sender = $this->userlist['names_by_id'][$this->playid]['department_id'];
        }

        $obj->subject = $this->getSubmittedVariableByName('temp_subject');
        $obj->rating = $this->getSubmittedVariableByName('temp_happiness');
        $obj->message = $this->getSubmittedVariableByName('temp_message');
        $obj->excellent = $this->getSubmittedVariableByName('temp_excellent');
        $obj->to_maintain = $this->getSubmittedVariableByName('temp_maintain');
        $obj->to_change = $this->getSubmittedVariableByName('temp_change');

        if($id){
            $obj->update();
        } else {
            $obj->insert();
            $this->feedbackNotify($this->sessionGet('selected_person'),'feedback',false,false,'show_msg_'.$obj->id);
        }

        $charcount = strlen($obj->message.$obj->excellent.$obj->to_maintain.$obj->to_change);

        $output[] = $this->getImage('msg-sent.png',array('margin' => '20 0 20 0','text-align' => 'center','height' => '120'));
        $output[] = $this->getText('+2pt',array('style' => 'places_toptext_grey'));

        if($charcount > 250){
            $output[] = $this->getText('{#bonus_for_long_feedback#} +1pt',array('style' => 'places_toptext_grey'));
            Aeplay::addSubtractPoints($this->playid,'primary',3,$this->gid);
        } else {
            Aeplay::addSubtractPoints($this->playid,'primary',2,$this->gid);
        }

        $output[] = $this->getText('{#message_sent#}',array('style' => 'places_toptext_grey'));
        $output[] = $this->getText('{#available_bonuses#}:',array('style' => 'places_toptext_grey'));
        $output[] = $this->getText('{#rated_useful#}: +2',array('style' => 'places_toptext_grey'));
        $output[] = $this->getText('{#new_user#}: +4',array('style' => 'places_toptext_grey'));
        $output[] = $this->getText('{#high_reciever_rating_gives_additional_boost#}',array('style' => 'places_toptext_grey'));

        $this->data->scroll[] = $this->getColumn($output,array('text-align' => 'center','width' => '100%'));

        $this->sessionSet('fundamental',false);
        $this->sessionSet('selected_person',false);
        $this->sessionSet('gif_url',false);
        $this->sessionSet('selected_mode',false);

        $clicks[] = $this->getOnclick('tab1');
        //$clicks[] = $this->getOnclick('list-branches');
        $clicks[] = $this->getOnclick('go-home',false);

        $col[] = $this->getText('{#ok#}!',array('onclick' => $clicks,
            'width' => '30%',
            'background-color' => $this->color_topbar,'border-radius' => '8',
            'padding' => '10 4 10 4','color' => '#ffffff','text-align' => 'center','text-size' => '14'));


        $this->data->footer[] = $this->getRow($col,array('text-align' => 'center','margin' => '10 0 10 0'));

    }



}