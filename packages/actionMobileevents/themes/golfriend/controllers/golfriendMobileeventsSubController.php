<?php

class golfriendMobileeventsSubController extends MobileeventsController {


    /* main listing view */
    public function tab1() {

        $this->saveVariables();
        $this->data = new stdClass();

/*       if($this->fake_play_error){
            $this->data->scroll[] = $this->getText('{#please_logout_and_login_back_again#}');
            return $this->data;
        }*/

        /* this is used to trigger creation of the event from another app */
        if($this->menuid == 'newround-otherapp' AND $this->current_tab != 3){
            //$this->data->onload[] = $this->getOnclick('tab3');
            //$this->handleNewEvent();
            //return $this->data;
        }

        $this->data->scroll[] = $this->getSettingsTitle('{#my_rounds#}');
        $this->getRounds('my');
        $this->data->scroll[] = $this->getSettingsTitle('{#invitations#}');
        $this->getRounds('invited');
        $this->data->scroll[] = $this->getSettingsTitle('{#rounds_i_participate_in#}');
        $this->getRounds('participating');

        $this->data->scroll[] = $this->getSettingsTitle('{#open_for_everyone#}');
        $this->data->scroll[] = $this->addSlider('{#max_distance#} (km)', 'invitation_distance', '50');
        $this->getRounds('open', $this->getSavedVariable('invitation_distance'));

        $this->data->scroll[] = $this->getButtonWithIcon('add-event-icon-white.png', 'add-event',
            '{#create_a_new_round#}', array('style' => 'oauth_button_style'), array('style' => 'fbbutton_text_style'), $this->getOnclick('tab3', true));
        $this->data->scroll[] = $this->getSpacer('10');

        return $this->data;
    }

    /* not used for now, but this can contain all open events if we want to move them from the main view */
    public function tab2() {
        $this->data = new StdClass();
/*        if($this->fake_play_error){
            $this->data->scroll[] = $this->getText('{#please_logout_and_login_back_again#}');
            return $this->data;
        }*/

        $this->setHeader();
        $this->data->scroll[] = $this->getSettingsTitle($this->current_tab);
        $this->data->scroll[] = $this->getSettingsTitle('{#all_open_rounds#}');
        $this->getRounds('open');
        return $this->data;
    }

    /* create new event */
    public function tab3() {
        $this->data = new StdClass();

        if($this->current_tab != 3){
            $this->data->scroll[] = $this->getFullPageLoader();
            return $this->data;
        }

/*        if($this->fake_play_error){
            $this->data->scroll[] = $this->getText('{#please_logout_and_login_back_again#}');
            return $this->data;
        }*/

        $this->saveVariables();
        $this->handleNewEvent();
        return $this->data;
    }

    public function handleNewEvent(){
        if ($this->menuid == 'add-event-3' AND $this->getSavedVariable('eventmp_club') > 0) {
            $this->data->scroll[] = $this->getSpacer('40');
            $this->data->scroll[] = $this->getLoader('', array('color' => '#000000'));
            $this->data->scroll[] = $this->getText('{#saving#} ...',array('text-align' => 'center'));
            $this->createNewEvent();
        } elseif ($this->menuid == 'add-event-2' AND $this->getSavedVariable('eventmp_club') > 0
            //OR $this->getSavedVariable('event_temp') /* good for debugging */
        ) {

            $this->data->scroll[] = $this->getSettingsTitle('{#my_rounds#} > {#add_new#} 2/2', array('onclick' => $this->getOnclick('tab1')));

            if ($this->getSavedVariable('eventmp_teeoff') == 1) {
                $this->data->scroll[] = $this->getSettingsTitle('{#tee_off_time#}');
                $this->data->scroll[] = $this->teeoffSelector();
            }

            $this->data->scroll[] = $this->getSettingsTitle('{#invites#}');
            $this->data->scroll[] = $this->getCheckBox('temp_open_everyone', '{#open_for_everyone#}');

            $this->setInviteeList();

            /* savers */
            $onclick1 = new stdClass();
            $onclick1->id = 'add-event-3';
            $onclick1->action = 'submit-form-content';
            $onclick2 = $this->getOnclick('tab1');

            $this->data->scroll[] = $this->getButtonWithIcon('add-event-icon-white.png', 'add-event-3', '{#create#}', array('style' => 'oauth_button_style'), array('style' => 'fbbutton_text_style'), array($onclick1, $onclick2));
            $this->data->scroll[] = $this->getSpacer('10');
        } elseif($this->menuid == 'add-event-2') {
            $this->data->scroll[] = $this->getSettingsTitle('{#my_rounds#} > {#add_new#} 1/2', array('onclick' => $this->getOnclick('tab1')));
            $this->data->scroll[] = $this->dateSelector();
            $this->data->scroll[] = $this->getText('{#club#}', array('style' => 'register-text-step-2'));
            $this->data->scroll[] = $this->addClubField();

            $this->data->scroll[] = $this->timeSelector();
            $this->data->scroll[] = $this->getText('{#notes#}', array('style' => 'register-text-step-2'));
            $this->data->scroll[] = $this->getFieldtextarea($this->getSavedVariable('eventmp_notes', ''), array('style' => 'list_container', 'variable' => $this->getVariableId('eventmp_notes')));
            $this->data->scroll[] = $this->getText('{#make_sure_you_select_a_club#}',array('style' => 'register-text-step-2'));
            $this->data->scroll[] = $this->getButtonWithIcon('add-event-icon-white.png', 'add-event-2', '{#create#}', array('style' => 'oauth_button_style'), array('style' => 'fbbutton_text_style'));
            $this->data->scroll[] = $this->getSpacer('10');
        } else {
            $this->data->scroll[] = $this->getSettingsTitle('{#my_rounds#} > {#add_new#} 1/2', array('onclick' => $this->getOnclick('tab1')));
            // $this->data->scroll[] = $this->getSettingsTitle('{#my_rounds#} > 1/2', array('onclick' => $this->getOnclick('tab1')));
            $this->data->scroll[] = $this->dateSelector();
            $this->data->scroll[] = $this->getText('{#club#}', array('style' => 'register-text-step-2'));
            $this->data->scroll[] = $this->addClubField();

            $notes = $this->getSavedVariable('eventmp_notes') ? $this->getSavedVariable('eventmp_notes') : '';

            $this->data->scroll[] = $this->timeSelector();
            $this->data->scroll[] = $this->getText('{#notes#}', array('style' => 'register-text-step-2'));
            $this->data->scroll[] = $this->getFieldtextarea($notes, array('style' => 'list_container', 'variable' => $this->getVariableId('eventmp_notes')));
            $this->data->scroll[] = $this->getSpacer('10');

            $onclick1 = new stdClass();
            $onclick1->id = 'main';
            $onclick1->action = 'open-tab';
            $onclick1->action_config = 1;

            $this->data->scroll[] = $this->getButtonWithIcon('cancel-icon-white.png', 'main', '{#cancel#}', array('style' => 'oauth_button_style'), array('style' => 'fbbutton_text_style','onclick' => $onclick1));
            $this->data->scroll[] = $this->getSpacer('10');
            $this->data->scroll[] = $this->getButtonWithIcon('add-event-icon-white.png', 'add-event-2', '{#create#}', array('style' => 'oauth_button_style'), array('style' => 'fbbutton_text_style'));
            $this->data->scroll[] = $this->getSpacer('10');
        }

    }



    /* choose the club */
    public function tab4() {
        $this->data = new StdClass();

        if (strstr($this->menuid, 'clubchoose_')) {
            $id = str_replace('clubchoose_', '', $this->menuid);
            $this->saveVariable('eventmp_club', $id);
        }

        $output[] = $this->getText(strtoupper('{#search_filtering#}'), array('style' => 'form-field-section-title'));

        $this->data = new stdClass();
        $this->data->scroll[] = $this->getText('{#find_a_club#}. {#you_can_use_the_search_above#}', array('style' => 'register-text-step-2'));

        $this->data->scroll[] = $this->getSpacer(9);
        $this->data->scroll[] = $this->getText('', array('style' => 'form-field-separator'));

        if ($this->menuid == 'searchbox' AND isset($this->submitvariables['searchterm']) AND strlen($this->submitvariables['searchterm']) < 3) {
            $this->data->scroll[] = $this->getText('{#write_at_least_three_letters_to_search#}', array(
                'style' => 'register-text-step-2'
            ));

        } elseif ($this->menuid == 'searchbox' AND isset($this->submitvariables['searchterm']) AND strlen($this->submitvariables['searchterm']) > 2) {
            $searchterm = $this->submitvariables['searchterm'];
            $wordlist = $this->mobileplacesobj->dosearch($searchterm);
            foreach ($wordlist as $word) {
                $this->setPlaceSimple($word);
            }
        } else {
            $wordlist = $this->mobileplacesobj->dosearch('', 8);
            foreach ($wordlist as $word) {
                $this->setPlaceSimple($word);
            }
        }

        $this->data->scroll[] = $this->getTextbutton('{#cancel#}', array('id' => 'cancel', 'action' => 'open-tab', 'config' => 3));
        $this->searchBox();
        return $this->data;
    }

    /* individual event view */
    public function tab5() {
        $this->data = new StdClass();

        if($this->current_tab == '5') {
            $this->showEvent();
        } else {
            $this->data->scroll[] = $this->getFullPageLoader();
        }

        return $this->data;
    }

    public function showEvent(){

        if(strstr($this->menuid,'showevent_')){
            $id = str_replace('showevent_','',$this->menuid);
            $this->saveVariable('event_id',$id);
        } else {
            $id = $this->getSavedVariable('event_id');
        }
        
        $font['text-align'] = 'center';
        $font['font-size'] = '18';
        $font['margin'] = '10 0 60 0';

        if ($this->current_tab != '5') {
            $this->data->scroll[] = $this->getSpacer('50');
            $this->data->scroll[] = $this->getLoader('', array('color' => '#000000', 'text-align' => 'center'));
        } elseif (strstr($this->menuid, 'do_cancel_round_')) {
            $id = str_replace('do_cancel_round_', '', $this->menuid);
            if ($id > 0) {
                MobileeventsModel::model()->deleteAllByAttributes(array('id' => $id, 'play_id' => $this->current_playid));
            }
        } elseif (strstr($this->menuid, 'confirm_cancel_round_')) {
            $id = str_replace('confirm_cancel_round_', '', $this->menuid);

            $this->data->scroll[] = $this->getSpacer('50');
            $this->data->scroll[] = $this->getText('{#delete_confirmation_for_round#}', $font);
            $this->data->scroll[] = $this->getButtonWithIcon('cancel-icon-white.png', 'do_cancel_round_' . $id, '{#yes_cancel#}',
                array('width' => '50%', 'style' => 'oauth_button_style'), array('style' => 'fbbutton_text_style'), $this->saveAndClose('do_cancel_round_' . $id));
            $this->data->scroll[] = $this->getSpacer(15);
        } elseif (strstr($this->menuid, 'notcoming_')) {
            $id = str_replace('notcoming_', '', $this->menuid);
            $this->eventobj->setStatus($id, $this->current_playid, 'not-coming');
            $this->setEventScreen($id);
        } elseif (strstr($this->menuid, 'coming_')) {
            $id = str_replace('coming_', '', $this->menuid);
            $this->eventobj->setStatus($id, $this->current_playid, 'coming');
            $this->setEventScreen($id);
        } else {
            $this->setEventScreen($id);
        }
    }

}