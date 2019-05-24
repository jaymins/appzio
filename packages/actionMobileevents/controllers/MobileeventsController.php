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
Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

class MobileeventsController extends ArticleController
{

    public $data;
    public $theme;

    /* @var MobileeventsModel */
    public $eventobj;

    /* @var MobileplacesModel */
    public $mobileplacesobj;

    public $participantcount;

    public $time_today;

    /* instead of using playid and gameid as usual, it is possible to associate
       everything to another play. This is to facilitate communication between
       two apps.
    */

    public $current_playid;
    public $current_gid;

    public function init(){

        /* we can be borrowing the data from another app */
        $this->fakePlay();

        $this->mobileplacesobj = new MobileplacesModel();
        $this->mobileplacesobj->playid = $this->current_playid;
        $this->mobileplacesobj->game_id = $this->current_gid;

        $this->eventobj = new MobileeventsModel();
        $this->eventobj->play_id = $this->current_playid;
        $this->eventobj->varcontent = $this->varcontent;

    }

    public function getRounds($which, $distance = false) {

        $events = $this->eventobj->getMyEvents($which);

        if (empty($events)) {
            return false;
        }

        foreach ($events as $event) {

            $today = date('Y-m-d', strtotime("today"));
            $tomorrow = date('Y-m-d', strtotime("tomorrow"));

            if (strtotime($event['date']) < strtotime("today")) {
                $this->eventobj->markOld($event['eventid']);
                continue;
            }

            /* filter by distance */
            if ($distance AND isset($event['distance'])) {
                if ($event['distance'] > $distance) {
                    continue;
                }
            }

            if ($today == $event['date']) {
                $string = '{#today#}';

                /* set the time of the first round */
                if(!$this->time_today){
                    $this->time_today = $event['date'] .' ' .$event['time'];
                }
            } elseif ($tomorrow == $event['date']) {
                $string = '{#tomorrow#}';
            } else {
                $string = $event['date'];
            }

            $style['padding'] = '8 8 8 8';
            $style['width'] = '22%';
            $style['font-size'] = '12';

            $col[] = $this->getImage('cal-icon-p.png', array('width' => '30', 'margin' => '5 10 10 15'));
            $col[] = $this->getText($string, $style);
            $style['width'] = '15%';
            $col[] = $this->getText(substr($event['time'], 0, 5), $style);
            $style['width'] = '55%';

            $dist = isset($event['distance']) ? ' (' . round($event['distance'], 0) . 'km)' : false;
            $col[] = $this->getText($event['name'] . $dist, $style);

            $onclick = new StdClass();
            $onclick->action = 'open-tab';
            $onclick->action_config = '5';
            $onclick->id = 'showevent_' .$event['eventid'];
            $onclick->back_button = 1;
            $onclick->sync_open = 1;

            $this->data->scroll[] = $this->getRow($col, array('vertical-aling' => 'middle', 'onclick' => $onclick));
            unset($col);
            unset($rr);
        }

        return true;
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

    protected function addSlider($title, $variablename, $defaultvalue) {

        $val = $this->getSavedVariable($variablename) ? $this->getSavedVariable($variablename) : $defaultvalue;
        $variableid = $this->getVariableId($variablename);

        $params = array('variable' => $variableid, 'min_value' => '10', 'max_value' => '500', 'value' => $val, 'step' => 1,
            'left_track_color' => '#a4c97f', 'right_track_color' => '#000000', 'margin' => '0 15 0 15',
            'track_height' => '1', 'vertical-align' => 'middle','width' => '80%',
            'submit_on_entry' => '1'
        );

        $params['value'] = $val;

        $rr[] = $this->getText(strtoupper($title), array('style' => 'form-field-titletext'));
        $rr[] = $this->getText($val . 'km', array('font-size' => '13', 'padding' => '2 25 0 0', 'floating' => '1', 'float' => 'right', 'variable' => $variableid));

        $col[] = $this->getRow($rr);
        $row[] = $this->getRangeslider('none', $params);
        $row[] = $this->getButtonWithIcon('search-icon-black.png', 'slider', '');

        $col[] = $this->getRow($row, array('margin' => '15 0 15 0', 'height' => '30', 'vertical-align' => 'middle'));
        $col[] = $this->getText('', array('style' => 'form-field-separator'));
        return $this->getColumn($col, array('style' => 'form-field-row'));

    }

    /* simple helper for getting a menu action and going back to main view */
    public function saveAndClose($id) {
        $onclick = new stdClass();
        $onclick->id = $id;
        $onclick->action = 'submit-form-content';
        $ret = array($onclick, $this->getOnclick('tab1'));
        return $ret;
    }

    public function getStyleDefaultButton() {
        $buttonparams['margin'] = '8 8 8 8';
        $buttonparams['padding'] = '8 8 8 8';
        $buttonparams['border-radius'] = '8';
        $buttonparams['width'] = '43%';
        $buttonparams['text-align'] = 'center';
        $buttonparams['background-color'] = '#d75959';
        return $buttonparams;
    }

    public function getStyleDefaultText() {
        $txtstyle['color'] = '#ffffff';
        $txtstyle['text-align'] = 'center';
        $txtstyle['font-size'] = '13';
        return $txtstyle;
    }

    public function getStyleDefaultTextBlack() {
        $txtstyle['color'] = '#000000';
        $txtstyle['text-align'] = 'center';
        $txtstyle['font-size'] = '13';
        return $txtstyle;
    }

    public function setEventScreen($id) {
        $font['text-align'] = 'center';
        $font['font-size'] = '18';

        $buttonparams = $this->getStyleDefaultButton();
        $txtstyle = $this->getStyleDefaultText();


        /* round info */
        $data = $this->eventobj->getEvent($id);
        $this->data->scroll[] = $this->getSpacer(50);
        $this->data->scroll[] = $this->getText(ucwords(strtolower($data['name'])), $font);
        $this->data->scroll[] = $this->getText(date('l', strtotime($data['date'])), $font);
        $font['font-size'] = '28';
        $this->data->scroll[] = $this->getText($data['date'], $font);
        $font['font-size'] = '18';
        $this->data->scroll[] = $this->getText('{#tee_off#} @ ' . substr($data['time'], 0, 5), $font);
        $this->data->scroll[] = $this->getSpacer(20);
        $font['font-size'] = '14';
        $this->data->scroll[] = $this->getText($data['notes'], $font);

        $this->data->scroll[] = $this->getSpacer(20);

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->config = $this->getConfigParam('place_view');
        $onclick->action_config = $this->getConfigParam('place_view');
        $onclick->sync_open = '1';
        $onclick->open_popup = '1';
        $onclick->id = $data['placeid'];

        $this->data->scroll[] = $this->getButtonWithIcon('golf-ball-icon.png', 'clubinfo', '{#show_club_info#}', array('width' => '50%', 'style' => 'oauth_button_style'), array('style' => 'fbbutton_text_style'), $onclick);

        if ($data['eventplayid'] == $this->current_playid) {
            $isadmin = true;
        } else {
            $isadmin = false;
        }

        /* participants */
        $this->data->scroll[] = $this->getSpacer(20);
        $this->data->scroll[] = $this->getSettingsTitle('{#participants#}');
        $this->setParticipants($id, $isadmin);

        if ($isadmin) {
            $this->data->scroll[] = $this->getButtonWithIcon('cancel-icon-white.png', 'confirm_cancel_round_' . $id, '{#cancel_round#}', array('width' => '50%', 'style' => 'oauth_button_style'), array('style' => 'fbbutton_text_style'));
            $this->data->scroll[] = $this->getSpacer(15);
/*        } elseif ($data['status'] == 'coming') {
            $buttonparams['width'] = '80%';
            $col[] = $this->getButtonWithIcon('cancel-icon-white.png', 'notcoming_' . $id, '{#cant_make_it#}', $buttonparams, $txtstyle, $this->saveAndClose('notcoming_' . $id));
            $this->data->scroll[] = $this->getRow($col, array('text-align' => 'center'));*/
        } elseif ($this->participantcount < 5) {
            $col[] = $this->getButtonWithIcon('cancel-icon-white.png', 'notcoming_' . $id, '{#cant_make_it#}', $buttonparams, $txtstyle, $this->saveAndClose('notcoming_' . $id));
            $col[] = $this->getVerticalSpacer('5');
            $buttonparams['background-color'] = '#56c656';
            $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'coming_' . $id, '{#coming#}', $buttonparams, $txtstyle);
            $this->data->scroll[] = $this->getRow($col, array('text-align' => 'center'));
        } else {
            $this->data->scroll[] = $this->getText('{#sorry_this_round_is_full#}', $font);
        }
    }

    public function setParticipants($id, $isadmin = false,$status=true) {
        $this->initMobileMatching();

        if ($isadmin) {
            $matches = MobileeventparticipantsModel::model()->findAllByAttributes(array('event_id' => $id));
            $count = MobileeventparticipantsModel::model()->findAllByAttributes(array('event_id' => $id, 'status' => 'coming'));
            $this->participantcount = count($count);
        } elseif($status) {
            $matches = MobileeventparticipantsModel::model()->findAllByAttributes(array('event_id' => $id, 'status' => 'coming'));
            $this->participantcount = count($matches);
        } else {
            $matches = MobileeventparticipantsModel::model()->findAllByAttributes(array('event_id' => $id));
            $this->participantcount = count($matches);
        }

        foreach ($matches as $match) {
            if($this->current_playid != $match->play_id){
                $vars = AeplayVariable::getArrayOfPlayvariables($match->play_id);
                $this->data->scroll[] = $this->getMyMatchItem($vars, $match->play_id, $match->status);
            }

        }
    }

    public function getMyMatchItem($vars, $id, $status) {

        /* this is very similar to what you would find from mymatches controller */
        $name = $this->getFirstName($vars);
        $name = isset($vars['city']) ? $name . ', ' . $vars['city'] : $name;

        $imageparams['style'] = 'round_image_imate';
        $imageparams['priority'] = 9;
        $imageparams['onclick'] = new StdClass();
        $imageparams['onclick']->action = 'open-action';
        $imageparams['onclick']->id = $id;
        $imageparams['onclick']->open_popup = 1;
        $imageparams['onclick']->sync_open = 1;
        $imageparams['onclick']->action_config = $this->requireConfigParam('user_profile_view');

        $textparams['font-size'] = '13';
        $textparams['padding'] = '0 10 0 8';

        $rowparams['padding'] = '0 10 0 0';
        $rowparams['margin'] = '0 20 0 20';
        $rowparams['vertical-align'] = 'middle';
        $rowparams['height'] = '100';
        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';

        if(isset($vars['profilepic'])){
            $filename = $this->getImageFileName($vars['profilepic']);
        }

        if(!isset($filename) OR !$filename){
            $profilepic = 'anonymous2.png';
        }

        $columns[] = $this->getImage($profilepic, $imageparams);
        $rr[] = $this->getText($name, $textparams);
        $rr[] = $this->getText($this->getAvailability($vars), $textparams);
        $columns[] = $this->getColumn($rr, array('vertical-align' => 'middle','width' => '60%'));
        $columns[] = $this->getUserStatusOrCheckbox($id,$status);

        return $this->getRow($columns, $rowparams);
    }

    public function getUserStatusOrCheckbox($id,$status) {
        if ($status) {
            $pp[] = $this->getText('{#status#}:', $this->getStyleDefaultTextBlack());
            $pp[] = $this->getText('{#' . $status . '#}', $this->getStyleDefaultTextBlack());
            return $this->getColumn($pp, array('vertical-align' => 'middle', 'width' => '20%'));
        } else {
            /* checkboxes, kinda like you would find from myplaces controller */
            $this->copyAssetWithoutProcessing('checkbox-icon-checked.png');
            $this->copyAssetWithoutProcessing('checkbox-icon-unchecked.png');

            $selectstate = array('style' => 'selector_checkbox_selected_events', 'active' => '0', 'variable_value' => '1', 'allow_unselect' => 1, 'animation' => 'fade');
            return $this->getText('', array('style' => 'selector_checkbox_unselected_events', 'variable' => 'temp_selection_' . $id, 'selected_state' => $selectstate));
        }
    }

    public function getFirstName($vars) {
        $name = isset($vars['real_name']) ? $vars['real_name'] : false;

        if ( empty($name) ) {
            return false;
        }

        $firstname = explode(' ', trim($vars['real_name']));
        $firstname = $firstname[0];
        return $firstname;
    }

    public function getAvailability($vars) {
        $availibility_info = '{#availability#}: {#n/a#}';

        if (isset($vars['availability'])) {
            $availability = json_decode($vars['availability'], true);

            if (count($availability) > 1) {

                $eventdate = explode(':', $this->getSavedVariable('eventmp_date'));
                $eventdate = strtolower($eventdate[0]);
                $timestring = $eventdate . '_' . $this->getSavedVariable('eventmp_time');

                if (isset($availability[$timestring])) {
                    $availibility_info = '{#availability#}: {#generally_available_at_this_time#}';
                } else {
                    $availibility_info = '{#availability#}: {#generally_not_available_at_this_time#}';
                }
            }
        }

        return $availibility_info;

    }

    public function createNewEvent() {

        if($this->fake_play_error){
            return false;
        }

        $obj = new MobileeventsModel();
        $obj->game_id = $this->current_gid;
        $obj->play_id = $this->current_playid;
        $obj->place_id = $this->getSavedVariable('eventmp_club');
        $obj->notes = $this->getSavedVariable('eventmp_notes');

        $obj->date = $this->getSavedVariable('eventmp_date');
        $obj->time = str_pad($this->getSubmittedVariableByName('teeoff_hour'), 2, '0') . ':' . str_pad($this->getSubmittedVariableByName('teeoff_minute'), 2, '0');
        $obj->time_of_day = $this->getSavedVariable('eventmp_time');

        if ($this->getSubmittedVariableByName('temp_open_everyone') AND $this->getSubmittedVariableByName('temp_open_everyone') == 1) {
            $obj->status = 'open-all';
        } else {
            $obj->status = 'open-private';
        }

        $obj->insert();
        $id = $obj->id;

        $ob2 = new MobileeventparticipantsModel();
        $ob2->play_id = $this->current_playid;
        $ob2->event_id = $id;
        $ob2->status = 'coming';
        $ob2->insert();

        foreach ($this->submitvariables as $key => $value) {
            if (strstr($key, 'temp_selection_') AND $value == 1) {
                $userid = str_replace('temp_selection_', '', $key);

                $txt = '{#you_have_an_invitation_to_play#}';
                $subject = '{#golf_invitation#}';
                Aenotification::addUserNotification( $userid, $subject, $txt, '1', $this->gid );

                $ob2 = new MobileeventparticipantsModel();
                $ob2->play_id = $userid;
                $ob2->event_id = $id;
                $ob2->status = 'invited';
                $ob2->insert();
            }
        }
    }

    public function teeoffSelector() {
        $cols[] = $this->getFieldlist('07;07;08;08;09;09;10;10;11;11;12;12;13;13;14;14;15;15;16;16;17;17;18;18;19;19;20;20;21;21;22;22;23;23',
            array('variable' => 'teeoff_hour', 'style' => 'datepicker', 'value' => $this->getSubmittedVariableByName('eventmp_time', '')));

        $cols[] = $this->getText(':', array('font-size' => '22'));
        $cols[] = $this->getFieldlist('00;00;05;05;10;10;15;15;20;20;25;25;30;30;35;35;40;40;45;45;50;50;55;55',
            array('variable' => 'teeoff_minute', 'style' => 'datepicker', 'value' => $this->getSubmittedVariableByName('eventmp_time', '')));
        return $this->getRow($cols, array('style' => 'list_container'));
    }

    public function getCheckBox($varname, $title, $error = false, $params = false) {
        $row[] = $this->getText(strtoupper($title), array('style' => 'form-field-textfield-onoff'));
        $row[] = $this->getFieldonoff('', array(
                'variable' => $varname,
                'margin' => '0 15 9 0',
                'floating' => '1',
                'float' => 'right'
            )
        );

        $columns[] = $this->getRow($row);
        $columns[] = $this->getText('', array('style' => 'form-field-separator'));

        return $this->getColumn($columns, array('style' => 'form-field-row'));
    }

    public function setInviteeList() {
        $this->initMobileMatching();
        $matches = $this->mobilematchingobj->getMyMatches();

        foreach ($matches as $match) {
            $vars = AeplayVariable::getArrayOfPlayvariables($match);
            $this->data->scroll[] = $this->getMyMatchItem($vars, $match, false);
        }
    }

    public function initMobileMatching($otheruserid=false,$debug=false){
        Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

        if($debug){
        }

        $this->mobilematchingobj = new MobilematchingModel();
        $this->mobilematchingobj->playid_thisuser = $this->current_playid;
        $this->mobilematchingobj->playid_otheruser = $otheruserid;
        $this->mobilematchingobj->gid = $this->current_gid;
        $this->mobilematchingobj->actionid = $this->actionid;
        $this->mobilematchingobj->uservars = $this->varcontent;
        $this->mobilematchingobj->factoryInit($this);
        $this->mobilematchingobj->initMatching($otheruserid,true);
    }

    public function dateSelector() {
        $start = strtotime('yesterday');
        $dateselector = '';

        $val = $this->getSavedVariable('eventmp_date', '');

        for ($i = 1; $i <= 14; $i++) {
            $dateselector .= date('Y-m-d', strtotime("+$i day", $start)) . ';';
            $dateselector .= '{#' . date('l', strtotime("+$i day", $start)) . '#} ' . date('j.n.', strtotime("+$i day", $start)) . ';';
        }

        $dateselector = substr($dateselector, 0, -1);

        $this->data->scroll[] = $this->getText('{#date#}', array('style' => 'register-text-step-2'));

        $args = array('variable' => 'eventmp_date', 'style' => 'wide_picker', 'value' => $val);

        $cols[] = $this->getFieldlist($dateselector, $args);
        return $this->getRow($cols, array('style' => 'list_container'));
    }

    public function addClubField() {
        /* chosen in another tab, from temp variable & lastly from users home club */

        if ($this->getSavedVariable('eventmp_club')) {
            $id = $this->getSavedVariable('eventmp_club');
        } elseif ($this->getSavedVariable('home_club')) {
            $id = $this->getSavedVariable('home_club');
        } else {
            $id = false;
        }

        if ($id) {
            $clubinfo = @MobileplacesModel::model()->findByPk($id);
            if (isset($clubinfo->name)) {
                $club_name = $clubinfo->name;
                $club_name = strtolower($club_name);
                $club_name = ucwords($club_name);
            }
        }

        if (!isset($club_name)) {
            $club_name = '{#please_choose#}';
        }

        $onclick3 = new stdClass();
        $onclick3->action = 'open-tab';
        $onclick3->action_config = 4;
        $onclick3->back_button = 1;

        $row[] = $this->getText($club_name, array('variable' => 'eventmp_club', 'hint' => '{#choose_the_club#}', 'style' => 'form-field-non-editable-textfield'));
        $row[] = $this->getImage('beak-icon.png', array('height' => '22', 'margin' => '10 20 0 0', 'opacity' => '0.6', 'floating' => 1, 'float' => 'right' ));
        $col[] = $this->getRow($row, array( 'vertical-align' => 'middle' ));

        return $this->getColumn($col, array('style' => 'club_field', 'onclick' => $onclick3));

    }

    public function timeSelector() {
        $this->data->scroll[] = $this->getText('{#time#}', array('style' => 'register-text-step-2'));

        $val = $this->getSavedVariable('eventmp_time');

        $active_any = $val == 'any' ? 1 : 0;
        $active_morning = $val == 'morning' ? 1 : 0;
        $active_afternoon = $val == 'afternoon' ? 1 : 0;

        $selectstate_any = array('variable_value' => "any", 'active' => $active_any, 'style' => 'selector_time_selected', 'allow_unselect' => 1, 'animation' => 'fade');
        $selectstate_morning = array('variable_value' => "morning", 'active' => $active_morning, 'style' => 'selector_time_selected', 'allow_unselect' => 1, 'animation' => 'fade');
        $selectstate_afternoon = array('variable_value' => "afternoon", 'active' => $active_afternoon, 'style' => 'selector_time_selected', 'allow_unselect' => 1, 'animation' => 'fade');

        $col[] = $this->getText('{#any#}', array('variable' => 'eventmp_time', 'selected_state' => $selectstate_any, 'style' => 'selector_time'));
        $col[] = $this->getVerticalSpacer('5%');
        $col[] = $this->getText('{#morning#}', array('variable' => 'eventmp_time', 'selected_state' => $selectstate_morning, 'style' => 'selector_time'));
        $col[] = $this->getVerticalSpacer('5%');
        $col[] = $this->getText('{#afternoon#}', array('variable' => 'eventmp_time', 'selected_state' => $selectstate_afternoon, 'style' => 'selector_time'));

        $this->data->scroll[] = $this->getRow($col, array('margin' => '4 40 13 40', 'text-align' => 'center'));
        $this->data->scroll[] = $this->getCheckBox2('eventmp_teeoff', '{#i_already_have_a_teeoff_time#}');
    }

    public function getCheckBox2($varname, $title, $error = false, $params = false) {

        $row[] = $this->getText(strtoupper($title), array("font-size" => "13",
            "padding" => "7 14 9 14"));

        $row[] = $this->getFieldonoff($this->getSavedVariable($varname), array(
                'value' => $this->getSavedVariable($varname),
                'variable' => $this->getVariableId($varname),
                'margin' => '4 40 5 40',
                'floating' => '1',
                'float' => 'right'
            )
        );

        $columns[] = $this->getRow($row);
        return $this->getColumn($columns, array('margin' => '0 0 0 35', 'vertical-align' => 'middle'));
    }

    public function setHeader() {
        $this->data->header[] = $this->getTabs(array('tab1' => '{#rounds#}', 'tab2' => '{#open_for_all#}'));
    }

    public function setPlaceSimple($data) {
        $onclick1 = new stdClass();
        $onclick1->action = 'submit-form-content';
        $onclick1->id = 'clubchoose_' . $data['id'];

        $onclick2 = new stdClass();
        $onclick2->action = 'open-tab';
        $onclick2->action_config = 3;

        $clickers = array($onclick1, $onclick2);

        if(!is_array($data)){
            return false;
        }

        if(!isset($data['logo']) OR $data['logo'] == 'dummylogo.png'){
            $data['logo'] = 'default-golf-logo.png';
        } else {
            $data['logo'] = basename($data['logo']);
        }

        $col[] = $this->getImage($data['logo'], array('width' => '15%', 'vertical-align' => 'middle','imgwidth' => '160', 'imgheight' => '160','priority' => 9,'imgcrop' => 'yes',
            'onclick' => $clickers));
        $col[] = $this->getPlaceRowPart($data, '70%');
        $col[] = $this->getImage('round-select-icon.png', array('opacity' => '0.5', 'margin' => '7 4 0 0'));
        $this->data->scroll[] = $this->getRow($col, array('margin' => '0 15 2 15', 'padding' => '5 5 5 5', 'background-color' => '#ffffff',
            'vertical-align' => 'middle', 'onclick' => $clickers));

        $this->data->scroll[] = $this->getText('', array('style' => 'form-field-separator'));
    }

    public function getPlaceRowPart($data, $width = '55%') {
        $distance = round($data['distance'], 0) . 'km';
        $row[] = $this->getText($data['name'], array('background-color' => '#ffffff', 'padding' => '3 5 3 5', 'color' => '#000000', 'font-size' => '12'));
        $row[] = $this->getText($data['county'], array('background-color' => '#ffffff', 'padding' => '0 5 3 5', 'color' => '#000000', 'font-size' => '11'));
        $row[] = $this->getText($data['city'] . ', ' . $distance, array('background-color' => '#ffffff', 'padding' => '0 5 3 5', 'color' => '#000000', 'font-size' => '11'));
        return $this->getColumn($row, array('width' => $width));
    }

    public function searchBox() {
        $value = $this->getSubmitVariable('searchterm') ? $this->getSubmitVariable('searchterm') : '';
        $row[] = $this->getImage('search-icon-for-field.png', array('height' => '25'));
        $row[] = $this->getFieldtext($value, array(
            'style' => 'example_searchbox_text',
            'hint' => '{#free_text_search#}', 'submit_menu_id' => 'searchbox', 'variable' => 'searchterm',
            //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
            'id' => 'something',
            'width' => '70%',
            'suggestions_style_row' => 'example_list_row', 'suggestions_text_style' => 'example_list_text',
            'submit_on_entry' => '1',
        ));
        $col[] = $this->getRow($row, array('style' => 'example_searchbox'));
        $col[] = $this->getTextbutton('Search', array('style' => 'example_searchbtn', 'id' => 'dosearch'));
        $this->data->header[] = $this->getRow($col, array( 'vertical-align' => 'middle', 'background-color' => $this->color_topbar ));
        $this->data->scroll[] = $this->getLoader('Loading', array('color' => '#000000', 'visibility' => 'onloading'));
    }

}