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

Yii::import('application.modules.aelogic.packages.actionMobileevents.models.*');
Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');
Yii::import('application.modules.aelogic.packages.actionMobileplaces.helpers.*');
Yii::import('application.modules.aelogic.packages.actionMobileevents.controllers.*');

class MobilegolfController extends MobileeventsController {

    public $data;
    public $theme;

    /* @var MobilegolfholeModel */
    public $holeobj;

    /* @var MobilegolfholeuserModel */
    public $hole_userobj;

    public $current_hole;
    public $event_id;
    public $place_id;

    public $course_par = 72;
    public $current_hole_user_obj;
    public $current_hole_obj;
    public $event_info;
    public $current_hole_order;
    public $errorState = false;
    public $hole_id;
    public $user_hole_id;
    public $my_course_par;
    public $my_score;

    public $golf_course_purchases;

    public function init(){
        $this->fakePlay(true);

        $this->holeobj = new MobilegolfholeModel();
        $this->hole_userobj = new MobilegolfholeuserModel();

        $this->holeobj->play_id = $this->current_playid;
        $this->hole_userobj->play_id = $this->current_playid;

        /* information about which courses are bought */
        $this->golf_course_purchases = json_decode($this->getSavedVariable('golf_course_purchases'),true);

        parent::init();
        $this->initEvent();
    }

    public function initEvent(){
        /* create the required objects */
        if(!$this->event_id){
            $this->event_id = $this->getSavedVariable('event_id');
        }

        if($this->event_id){
            /* we save the currently active event */
            if($this->event_id != $this->getSavedVariable('event_id')){
                $this->saveVariable('event_id',$this->event_id);
            }

            $this->event_info = $this->eventobj->getEvent($this->event_id);
            $this->place_id = $this->event_info['place_id'];

            /* the info includes useful fields that can be used by the userobj */
            $this->hole_userobj->event_info = $this->event_info;
            $this->holeobj->event_info = $this->event_info;

            /* basically an init function */
            $this->hole_userobj->setCurrentHole($this->event_id);
            $this->current_hole = $this->hole_userobj->getCurrentHoleNumber();
            $this->hole_id = $this->hole_userobj->getHoleId();
            $this->user_hole_id = $this->hole_userobj->getUserHoleId();

            /* if it exists, we start using that as the object */
/*            if(is_object($myhole)){
                $this->hole_userobj = $myhole;
            }*/

            /* init error, change action to error mode */
            if(!empty($this->hole_userobj->errors)){
                $this->displayErrors($this->hole_userobj->errors);
                return true;
            }

            /* we switch to hole object which is for current hole */
            if(is_object($this->hole_userobj->holeobj)){
                $this->holeobj = $this->hole_userobj->holeobj;
            }
        }
    }


    public function tab1(){
        $this->data = new stdClass();
        $this->fakePlay(true);

        $this->askLocation();
        $this->askPushPermission();
        $this->askMonitorRegion($this->getConfigParam('monitor_region'));

        $menus = array_flip($this->menuitems);

        if(is_numeric($this->menuid) AND isset($menus[$this->menuid]) AND $menus[$this->menuid] == 'cancel_round'){
            $this->cancelRound();
            return $this->data;
        }

        if(strstr($this->menuid,'startround2_')){
            $id = str_replace('startround2_','',$this->menuid);
            $this->startRound($id);
            return $this->data;
        }

        if(strstr($this->menuid,'complete_hole_')){
            $id = str_replace('complete_hole_','',$this->menuid);
            $this->completeHole($id);
            return $this->data;
        }

        if($this->menuid == 'hole_summary'){
            $this->holeSummary();
            return $this->data;
        }

        if($this->menuid == 'next_hole'){
            $this->nextHole();
            return $this->data;
        }

        if($this->menuid == 'skip_hole'){
            $this->nextHole(true);
            return $this->data;
        }

        if($this->menuid == 'close_round'){
            $this->closeRound();
            return $this->data;
        }

        $this->homeView();

        if ($this->getConfigParam('google_adcode_banners')){
            $this->data->footer[] = $this->getBanner($this->getConfigParam('google_adcode_banners'));
        }

        return $this->data;
    }






    /* this is main view */
    public function homeView($list=false) {
        $events = $this->eventobj->getMyEvents('on-going');

      //  print_r($events);die();

        /* so if we have an on-going event this turns into home view
            on-going events can only happen during the same day
        */

        if(!empty($events) AND isset($events[0]['eventid']) AND !$list){
            $this->event_id = $events[0]['eventid'];
            $this->roundHome();
            return true;
        } else {
            $this->listEvents();
            return true;
        }
    }


    public function cancelRound(){
        $this->initEvent();
        $this->setHeader();
        $this->data->scroll[] =  $this->getText('{#round_started_at#} '.$this->event_info['starting_time'],array('style' => 'golf_hole_header'));
        $this->data->scroll[] = $this->getSpacer('80');
    
        if($this->hole_userobj->getStrokes() == '0'){
            $this->data->scroll[] = $this->getText('{#are_you_sure_you_want_to_quit_the_entire_round#}?',array('style' => 'golf_swing_counter'));
        } else {
            $this->data->scroll[] = $this->getText('{#are_you_sure_you_want_to_quit_the_entire_round#}?',array('style' => 'golf_swing_counter'));
        }

        $buttonparams = $this->getStyleDefaultButton();
        $buttonparams['background-color'] = '#56c656';
        $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'cancel', '{#go_back#}', $buttonparams, $this->getStyleDefaultText());
        $col[] = $this->getVerticalSpacer('15');
        $buttonparams = $this->getStyleDefaultButton();
        $col[] = $this->getButtonWithIcon('cancel-icon-white.png', 'close_round', '{#quit_round#}', $buttonparams, $this->getStyleDefaultText());
        $this->data->footer[] = $this->getRow($col, array('text-align' => 'center'));

    }


    public function nextHole($skip=false)
    {
        $this->initEvent();


        if ($skip) {
            $this->hole_userobj->nextHole(true);
            /* calling the init again */
            $this->initEvent();
        /* safeguard against double presses etc. */
        } elseif($this->hole_userobj->getStrokes() > 0){
            $this->hole_userobj->nextHole();
            /* calling the init again */
            $this->initEvent();
        }

        if($this->hole_userobj->end_of_round == true){
            $this->roundSummary();
        } else {
            $this->roundHome();
        }

        return true;

    }

    public function roundSummary(){
        $this->initEvent();
        $this->setHeader();
        $this->setScoreCard(true);

        if($this->my_course_par == $this->my_score){
            $txt = '{#wow_exactly_to_your_handicap#}';
        } elseif($this->my_course_par > $this->my_score){
            $txt = '{#well_done#}!';
        } else {
            $txt = '{#didnt_quite_make_it_to_your_level#}';
        }

        $this->data->scroll[] = $this->getText($txt,array('margin' => '20 0 10 0','text-align' => 'center', 'font-size' => '28'));
        $this->data->footer[] = $this->btnSummary();
    }

    public function btnSummary(){

        $onclick = new stdClass();
        $onclick->action = 'share';

        $title = '{#check_this_out#}';
        $txt = '{#i_scored_a#}';

        //$imageurl = Controller::getDomain() .'/documents' .DIRECTORY_SEPARATOR .'games' .DIRECTORY_SEPARATOR .$this->gid .'/images/'.$image;
        $this->rewriteActionConfigField('share_title',$this->localizationComponent->smartLocalize($title));
        $this->rewriteActionConfigField('share_description',$this->localizationComponent->smartLocalize($txt));
        //$this->rewriteActionConfigField('share_image',$imageurl);

        $buttonparams = $this->getStyleDefaultButton();
        $buttonparams['background-color'] = '#3b5998';
        $col[] = $this->getButtonWithIcon('share-icon-general.png', 'cancel_hole_finish', '{#share_results#}', $buttonparams, $this->getStyleDefaultText(),$onclick);
        $col[] = $this->getVerticalSpacer('15');
        $buttonparams['background-color'] = '#56c656';
        $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'close_round', '{#archive_round#}', $buttonparams, $this->getStyleDefaultText());
        return $this->getRow($col, array('text-align' => 'center'));
    }

    public function closeRound(){
        $this->initEvent();
        $ob = MobileeventparticipantsModel::model()->findByAttributes(array('play_id' => $this->current_playid,'event_id'=>$this->event_id));

        if(is_object($ob)){
            $ob->status = 'finished';
            $ob->update();
        }

        $this->homeView(true);
    }


    public function holeSummary(){
        $this->initEvent();
        $this->setHeader();
        $this->data->scroll[] =  $this->getText('{#round_started_at#} '.$this->event_info['starting_time'],array('style' => 'golf_hole_header'));

        /* hole in one */
        if($this->hole_userobj->getStrokes() == 1) {
            $image = 'hole-in-one.gif';
            $txt = '{#hole_in_one#}';
            $sharetext = '{#share_my_feat#}';
            /* albatross */
        } elseif($this->holeobj->par-3 == $this->hole_userobj->getStrokes()){
            $image = 'funny-golf-animated-gif-8.gif';
            $txt = '{#albatross#}';
            $sharetext = '{#share_my_feat#}';
            /* eagle */
        } elseif($this->holeobj->par-2 == $this->hole_userobj->getStrokes()){
            $image = 'funny-golf-animated-gif-8.gif';
            $txt = '{#eagle#}';
            $sharetext = '{#share_my_feat#}';
            /* birdie */
        } elseif($this->holeobj->par-1 == $this->hole_userobj->getStrokes()) {
            $image = 'well-done.gif';
            $txt = '{#birdie#}';
            $sharetext = '{#share_my_feat#}';
            /* par */
        } elseif($this->holeobj->par == $this->hole_userobj->getStrokes()){
            $image = 'par.gif';
            $txt = '{#par#}';
            $sharetext = '{#share_my_feat#}';
            /* bogey */
        } elseif($this->holeobj->par+1 == $this->hole_userobj->getStrokes()){
            $image = 'bogey.gif';
            $txt = '{#bogey#}';
            $sharetext = '{#share_my_score#}';
            /* double bogey */
        } elseif($this->holeobj->par+2 == $this->hole_userobj->getStrokes()){
            $image = 'bogey.gif';
            $txt = '{#double_bogey#}';
            $sharetext = '{#share_my_score#}';
        } elseif($this->holeobj->par+3 == $this->hole_userobj->getStrokes() AND $this->getSavedVariable('hcp') > 35){
            $image = 'bogey.gif';
            $txt = '{#triple_bogey#}';
            $sharetext = '{#share_my_score#}';
            /* worse */
        } else {
            $image = 'worst-performance.gif';
            $txt = '{#better_next_time#}';
            $sharetext = '{#share_my_humiliation#}';
        }

        $this->data->scroll[] = $this->getImage($image);
        $this->data->scroll[] = $this->getSpacer('40');
        $this->data->scroll[] = $this->getText($txt,array('font-size' => '32','text-align' => 'center'));
        $this->data->footer[] = $this->btnStartNewHole($sharetext,$image,$txt);
    }


    public function btnStartNewHole($sharetext,$image,$txt){

        $onclick = new stdClass();
        $onclick->action = 'share';

        $image = $this->getImageFileName($image);

        $title = '{#check_this_out#}';
        $txt = '{#i_scored_a#} '.$txt;

        $imageurl = Controller::getDomain() .'/documents' .DIRECTORY_SEPARATOR .'games' .DIRECTORY_SEPARATOR .$this->gid .'/images/'.$image;
        $this->rewriteActionConfigField('share_title',$this->localizationComponent->smartLocalize($title));
        $this->rewriteActionConfigField('share_description',$this->localizationComponent->smartLocalize($txt));
        $this->rewriteActionConfigField('share_image',$imageurl);

        $buttonparams = $this->getStyleDefaultButton();
        $buttonparams['background-color'] = '#3b5998';
        $col[] = $this->getButtonWithIcon('share-icon-general.png', 'cancel_hole_finish', $sharetext, $buttonparams, $this->getStyleDefaultText(),$onclick);
        $col[] = $this->getVerticalSpacer('15');
        $buttonparams['background-color'] = '#56c656';
        $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'next_hole', '{#next_hole#}', $buttonparams, $this->getStyleDefaultText());
        return $this->getRow($col, array('text-align' => 'center'));
    }


    public function completeHole($id){
        $this->initEvent();
        $this->setHeader();
        $this->data->scroll[] =  $this->getText('{#round_started_at#} '.$this->event_info['starting_time'],array('style' => 'golf_hole_header'));
        $this->data->scroll[] = $this->completeTop();
        $this->data->scroll[] = $this->getSpacer('30');

        if($this->hole_userobj->getStrokes() == '0'){
            $this->data->scroll[] = $this->getText('{#no_shots_recorded#}',array('style' => 'golf_swing_counter'));
        } else {
            $this->data->scroll[] = $this->getText('{#are_you_sure_you_want_to_mark_this_played#}?',array('style' => 'golf_swing_counter'));
        }

        $buttonparams = $this->getStyleDefaultButton();
        $col[] = $this->getButtonWithIcon('cancel-icon-white.png', 'cancel_hole_finish', '{#cancel#}', $buttonparams, $this->getStyleDefaultText());
        $col[] = $this->getVerticalSpacer('15');

        if($this->hole_userobj->getStrokes() != '0') {
            $buttonparams['background-color'] = '#56c656';
            $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'hole_summary', '{#confirm#}', $buttonparams, $this->getStyleDefaultText());
        } else {
            $col[] = $this->getButtonWithIcon('skip-icon-ffwd.png', 'skip_hole', '{#skip_hole#} (+10)', $buttonparams, $this->getStyleDefaultText());
        }

        $this->data->footer[] = $this->getRow($col, array('text-align' => 'center'));

    }

    public function completeTop(){

        $col[] = $this->getText($this->hole_userobj->getStrokes(),array('style' => 'golf_header_round_number'));
        $col[] = $this->getText('{#hole_total#}',array('style' => 'golf_swing_counter'));
        $items[] = $this->getColumn($col,array('text-align' => 'center','width' => '100%'));
        unset($col);

        return $this->getRow($items,array('text-align' => 'center','padding' => '20 0 10 0','background-color' => '#dbdbdb'));
    }


    /* this is main view for individual round */
    public function roundHome(){

        if($this->errorState == true){
            return false;
        }

        if($this->hole_userobj->end_of_round == true) {
            $this->roundSummary();
            return true;
        }

        $this->setHeader();
        $this->data->scroll[] =  $this->getText('{#round_started_at#} '.$this->event_info['starting_time'],array('style' => 'golf_hole_header'));
        $this->dataSaving();

        $this->data->scroll[] = $this->roundTop();
        $this->data->scroll[] = $this->getSpacer(45);
        $this->data->scroll[] = $this->btnCompleteHole();
        $this->data->scroll[] = $this->btnPlusMinus();
        $this->data->footer[] = $this->getHoleInfo();
    }

    public function listEvents(){
        $this->data->scroll[] = $this->getSettingsTitle('{#playing_today#}');
        $this->getRounds('today');
        $this->setTeeOffTimer();
        $this->data->scroll[] = $this->getSpacer('20');
        $this->mobileplacesobj = new MobileplacesModel();
        $this->mobileplacesobj->playid = $this->current_playid;
        $this->mobileplacesobj->game_id = $this->gid;
/*        $closest_club = $this->mobileplacesobj->dosearch('',1);
        $this->data->scroll[] = $this->getSettingsTitle('{#start_round_now_at#} '.$closest_club[0]['name']);*/

        $this->data->scroll[] = $this->getSettingsTitle('{#archived#}');
        $this->getRounds('archived');

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->sync_open = 1;
        $onclick->action_config = $this->getConfigParam('events_action');
        $onclick->id = 'newround';

        $onclick2 = new stdClass();
        $onclick2->action = 'open-tab';
        $onclick2->action_config = 3;

        $this->data->footer[] = $this->getButtonWithIcon(
            'add-event-icon-white.png', 'add-event', '{#create_a_new_round#}', array('style' => 'oauth_button_style'),
            array('style' => 'fbbutton_text_style'),array($onclick,$onclick2));
        $this->data->footer[] = $this->getSpacer('10');

    }


    public function tab2(){
        $this->data = new stdClass();
        $this->setHeader();
        $this->data->scroll[] = $this->getImage('hole-sample-' .$this->current_hole .'.jpg',array('width' => '100%','priority' => 9));
        return $this->data;
    }

    public function tab3(){
        $this->data = new stdClass();
        $this->setHeader();
        $this->data->scroll[] = $this->getImage('map-sample-golf.jpg',array('width' => '100%','priority' => 9));
        return $this->data;
    }

    /* aka the score card */

    public function tab4(){
        $this->data = new stdClass();
        $this->setHeader();
        $this->setScoreCard();
        return $this->data;
    }

    public function setScoreCard($inline=false){
        $scorecard = $this->hole_userobj->getScoreCard();
        $count = count($scorecard);

        $col[] = $this->getText('{#hole#}',array('style' => 'golf_scorecard_cell_header'));
        $col[] = $this->getText('{#hcp#}',array('style' => 'golf_scorecard_cell_header'));
        $col[] = $this->getText('{#par#}',array('style' => 'golf_scorecard_cell_header'));
        $col[] = $this->getText('{#my_par#}',array('style' => 'golf_scorecard_cell_header'));
        $col[] = $this->getText('{#score#}',array('style' => 'golf_scorecard_cell_header'));
        $this->data->scroll[] = $this->getRow($col,array('width' => '100%'));
        unset($col);

        $parcount = 0;
        $scorecount = 0;
        $myparcount = 0;

        foreach ($scorecard as $hole){

            $weighted_par = $this->weightedPar($hole['par'],$hole['hcp'],$count);

            $myparcount = $myparcount+$weighted_par;
            $scorecount = $scorecount+$hole['strokes'];
            $parcount = $parcount+$hole['par'];

            $col[] = $this->getText($hole['number'],array('style' => 'golf_scorecard_cell_number'));
            $col[] = $this->getText($hole['hcp'],array('style' => 'golf_scorecard_cell'));
            $col[] = $this->getText($hole['par'],array('style' => 'golf_scorecard_cell'));
            $col[] = $this->getText($weighted_par,array('style' => 'golf_scorecard_cell'));

            if(!$hole['strokes']){
                $col[] = $this->getText($hole['strokes'],array('style' => 'golf_scorecard_cell'));
            }elseif($weighted_par >= $hole['strokes']){
                $col[] = $this->getText($hole['strokes'],array('style' => 'golf_scorecard_cell_good'));
            } else {
                $col[] = $this->getText($hole['strokes'],array('style' => 'golf_scorecard_cell_bad'));
            }

            $this->data->scroll[] = $this->getRow($col,array('width' => '100%'));
            unset($col);

        }

        $col[] = $this->getText('',array('style' => 'golf_scorecard_cell_totals'));
        $col[] = $this->getText('',array('style' => 'golf_scorecard_cell_totals'));
        $col[] = $this->getText($parcount,array('style' => 'golf_scorecard_cell_totals'));
        $col[] = $this->getText($myparcount,array('style' => 'golf_scorecard_cell_totals'));
        $col[] = $this->getText($scorecount,array('style' => 'golf_scorecard_cell_totals'));

        $this->course_par = $parcount;
        $this->my_course_par = $myparcount;
        $this->my_score = $scorecount;

        $col2[] = $this->getText('',array('style' => 'golf_totalstext'));
        $col2[] = $this->getText('',array('style' => 'golf_totalstext'));
        $col2[] = $this->getText('{#par#}',array('style' => 'golf_totalstext'));
        $col2[] = $this->getText('{#my_par#}',array('style' => 'golf_totalstext'));
        $col2[] = $this->getText('{#score#}',array('style' => 'golf_totalstext'));

        if($inline){
            $this->data->scroll[] = $this->getRow($col2,array('width' => '100%'));
            $this->data->scroll[] = $this->getRow($col,array('width' => '100%'));
        } else {
            $this->data->footer[] = $this->getRow($col,array('width' => '100%'));
            $this->data->footer[] = $this->getRow($col2,array('width' => '100%'));
        }

    }


    public function weightedPar($par,$hcp,$holecount){
        $myhcp = $this->getSavedVariable('hcp');

        if($holecount == '9'){
            $myhcp = $myhcp / 2;
        }

        $adder = $myhcp/$holecount;
        $adder = round($adder,0);
        $leftovers = $myhcp - $holecount*$adder;

        if($holecount-$hcp < $leftovers){
            $adder++;
        }

        return $par+$adder;
    }

    public function setHeader()
    {
        $this->data->header[] = $this->getTabs(array('tab1' => '{#record#}', 'tab2' => '{#hole#}', 'tab3' => '{#course#}', 'tab4' => '{#score#}'));
    }


    public function dataSaving(){
        switch($this->menuid){
            case 'plus':
                $this->hole_userobj->addOne();
                break;

            case 'minus':
                $this->hole_userobj->minusOne();
                break;
        }

    }


    public function roundTop(){
        $col[] = $this->getText($this->getSavedVariable('hcp')+$this->hole_userobj->getCoursePar(),array('style' => 'golf_header_round_number'));
        $col[] = $this->getText('{#my_par#}',array('style' => 'golf_swing_counter'));
        $items[] = $this->getColumn($col,array('text-align' => 'center','width' => '30%'));
        unset($col);

        $col[] = $this->getText($this->hole_userobj->round_strokes,array('style' => 'golf_header_round_number'));
        $col[] = $this->getText('{#my_total#}',array('style' => 'golf_swing_counter'));
        $items[] = $this->getColumn($col,array('text-align' => 'center','width' => '30%'));
        unset($col);

        $col[] = $this->getText($this->hole_userobj->getCoursePar(),array('style' => 'golf_header_round_number'));
        $col[] = $this->getText('{#course_par#}',array('style' => 'golf_swing_counter'));
        $items[] = $this->getColumn($col,array('text-align' => 'center','width' => '30%'));
        unset($col);

        return $this->getRow($items,array('text-align' => 'center','padding' => '20 0 10 0','background-color' => '#dbdbdb'));
    }


    public function btnCompleteHole(){
        $onclick = new stdClass();
        $onclick->id = 'complete_hole_'.$this->user_hole_id;
        $onclick->action = 'submit-form-content';
        $hole[] =  $this->getText('{#complete_hole#}',array('style' => 'golf_control_complete','onclick' => $onclick));
        return $this->getRow($hole,array('margin' => '20 0 10 0','text-align' => 'center'));
    }

    public function btnPlusMinus(){

        $minus = new stdClass();
        $minus->id = 'minus';
        $minus->action = 'submit-form-content';

        $plus = new stdClass();
        $plus->id = 'plus';
        $plus->action = 'submit-form-content';

        $col[] = $this->getText('-',array('style' => 'golf_control_minusplus','onclick' => $minus));
        $rr[] = $this->getText($this->hole_userobj->getStrokes(),array('style' => 'golf_control_swings'));
        $rr[] = $this->getText('{#this_hole#}',array('style' => 'golf_swing_counter'));
        $col[] = $this->getColumn($rr);
        $col[] = $this->getText('+',array('style' => 'golf_control_minusplus','onclick' => $plus));
        $onemore[] = $this->getRow($col,array('margin' => '20 0 10 0','text-align' => 'center','background-color' => '#dbdbdb','width' => '250','border-radius' => '8'));
        return $this->getColumn($onemore,array('text-align' => 'center'));
    }


    public function getHoleInfo() {


        $row[] = $this->getText($this->holeobj->number,array('style' => 'golf_header_number'));
        $row[] = $this->getText(strtoupper('{#hole#}'),array('style' => 'golf_hole_par'));
        $col1[] = $this->getColumn($row,array('width' => '32%'));
        unset($row);

        $col1[] = $this->getVerticalSpacer('2%');

        $row[] = $this->getText($this->holeobj->par,array('style' => 'golf_header_number'));
        $row[] = $this->getText(strtoupper('{#par#}'),array('style' => 'golf_hole_par'));
        $col1[] = $this->getColumn($row,array('width' => '32%'));
        unset($row);

        $col1[] = $this->getVerticalSpacer('2%');

        $row[] = $this->getText($this->holeobj->hcp,array('style' => 'golf_header_number'));
        $row[] = $this->getText(strtoupper('{#hcp#}'),array('style' => 'golf_hole_par'));
        $col1[] = $this->getColumn($row,array('width' => '32%'));
        unset($row);

        $col[] = $this->getRow($col1);

        $row[] = $this->getText($this->holeobj->length_pro .'m',array('style' => 'golf_length_pro'));
        $row[] = $this->getText($this->holeobj->length_men .'m',array('style' => 'golf_length_men'));
        $row[] = $this->getText($this->holeobj->length_women .'m',array('style' => 'golf_length_women'));
        $row[] = $this->getText($this->holeobj->length_junior .'m',array('style' => 'golf_length_junior'));
        $col[] = $this->getRow($row,array('width' => '100%'));

        return $this->getColumn($col);
    }


    /* step 1, choosing course */
    public function chooseCourse(){

    }

    /* step 2, choosing players from friends or enter manually */
    public function choosePlayers($id){
        $this->data->scroll[] = $this->getSettingsTitle('‹ {#back#}',array('onclick' => $this->getOnclick('tab1')));
        $this->data->scroll[] = $this->getText('{#choose_who_is_playing_with_you#}',$this->getStyleDefaultTextBlack());
        $this->setParticipants($id,false,false);
        $buttonparams = $this->getStyleDefaultButton();
        $buttonparams['background-color'] = '#56c656';
        $buttonparams['width'] = '90%';

        $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'startround2_' . $id, '{#start_round#}', $buttonparams, $this->getStyleDefaultText(),array($this->getOnclick('tab1'),$this->onclicksForRoundStart($id,2)));
        $this->data->footer[] = $this->getRow($col, array('text-align' => 'center'));
    }

    /* step 3, starting the round */
    public function startRound($id){

        /* update all player statuses */
        foreach($this->submitvariables AS $key=>$val){
            if(strstr($key,'temp_selection_') AND $val == 1){
                $playid = str_replace('temp_selection_','',$key);
                $ob = MobileeventparticipantsModel::model()->findByAttributes(array('play_id' => $playid,'event_id'=>$id));

                if(is_object($ob)){
                    $ob->status = 'participating';
                    $ob->update();
                }
            }
        }

        /* author participating */
        $ob = MobileeventparticipantsModel::model()->findByAttributes(array('play_id' => $this->current_playid,'event_id'=>$id));

        if(!is_object($ob)){
            $ob = new MobileeventparticipantsModel();
            $ob->play_id = $this->current_playid;
            $ob->event_id = $id;
            $ob->status = 'participating';
            $ob->insert();
        } else {
            $ob->status = 'participating';
            $ob->update();
        }

        /* update the event status */
        $obj = MobileeventsModel::model()->findByPk($id);
        if(is_object($obj)){
            $obj->status = 'on-going';
            $obj->starting_time = date('Y-m-d H:i:s');
            $obj->update();
        }

        $this->initEvent();
        $this->roundHome();
    }

    /* step 4, payment gateway */
    public function paymentGate($id){
        $buttonparams = $this->getStyleDefaultButton();
        $buttonparams['background-color'] = '#56c656';
        $buttonparams['width'] = '90%';

        if(!$this->getSavedVariable('purchase_discountgreenfee') AND !$this->getSavedVariable('discount_discountandmap')){
            $btns[] = $this->getImage('scorecard-icon.png',array('text-align' => 'center','margin' => '30 0 20 0','width' => '120','height' => '120'));
            $btns[] = $this->getButtonWithIcon('golf-ball-icon.png', 'startround_' . $id, '{#buy_map_and_scorecard_for_this_course#} €3.99', $buttonparams, $this->getStyleDefaultText(),MobilegolfModel::purchaseButton('€3.99',$id));
            $btns[] = $this->getText('{#explanation_for_map_and_scorecard#}',$this->getStyleDefaultTextBlack());
            $btns[] = $this->getSpacer(15);
/*          $btns[] = $this->getButtonWithIcon('golf-ball-icon.png', 'startround_' . $id, '{#buy_map_and_scorecard_and_get_discounts#} €7.99', $buttonparams, $this->getStyleDefaultText(),MobilegolfModel::purchaseButton('€7.99',$id));
            $btns[] = $this->getText('{#explanation_for_map_and_scorecard_with_discounts#}',$this->getStyleDefaultTextBlack());
            $btns[] = $this->getSpacer(15);*/
            $btns[] = $this->getButtonWithIcon('golf-ball-icon.png', 'startround_' . $id, '{#restore_previous_purchase#}', $buttonparams, $this->getStyleDefaultText(),MobilegolfModel::purchaseButton('restore',$id));
            $btns[] = $this->getText('{#explanation_for_restore#}',$this->getStyleDefaultTextBlack());
            $this->data->scroll[] = $this->getColumn($btns,array('margin' => '0 40 0 40','text-align' => 'center'));
        } else {

            $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'startround_' . $id, '{#start_round#}', $buttonparams, $this->getStyleDefaultText(),$this->onclicksForRoundStart());
            $this->data->footer[] = $this->getRow($col, array('text-align' => 'center'));
        }
    }

    /* step 4, payment gateway */
    public function paymentScreen($id){

        $buttonparams = $this->getStyleDefaultButton();
        $buttonparams['background-color'] = '#56c656';
        $buttonparams['width'] = '90%';
        $ids = explode('-',$id);

        if(!isset($ids[0]) OR !isset($ids[1])){
            $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'startround_' . $id, '{#start_round#}', $buttonparams, $this->getStyleDefaultText(),$this->onclicksForRoundStart());
            $btns[] = $this->getText('{#an_unknown_error_has_happened#}',$this->getStyleDefaultTextBlack());
            $this->data->footer[] = $this->getRow($col, array('text-align' => 'center'));
            return false;
        }

        $id = $ids[1];
        $product = $ids[0];
        $varname = 'purchase_'.$product;

        /* purchase was a success */
        if($this->getSavedVariable($varname)){
            MobilegolfModel::addPurchase($this->gid,$this->current_playid,$id);
            $this->deleteVariable($varname);
            $btns[] = $this->getImage('scorecard-icon.png',array('text-align' => 'center','margin' => '30 0 20 0','width' => '120','height' => '120'));
            $btns[] = $this->getText($id,$this->getStyleDefaultTextBlack());
            $this->data->scroll[] = $this->getColumn($btns,array('margin' => '0 40 0 40','text-align' => 'center'));

            $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'startround_' . $id, '{#start_round#}', $buttonparams, $this->getStyleDefaultText(),$this->onclicksForRoundStart());
            $this->data->footer[] = $this->getRow($col, array('text-align' => 'center'));
        } else {
            $btns[] = $this->getImage('scorecard-icon.png',array('text-align' => 'center','margin' => '30 0 20 0','width' => '120','height' => '120'));
            $btns[] = $this->getText('{#sorry_there_was_an_error_with_the_purchase#}',$this->getStyleDefaultTextBlack());
            $this->data->scroll[] = $this->getColumn($btns,array('margin' => '0 40 0 40','text-align' => 'center'));

            $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'startround_' . $id, '{#go_back#}', $buttonparams, $this->getStyleDefaultText(),$this->getOnclick('tab1'));
            $this->data->footer[] = $this->getRow($col, array('text-align' => 'center'));
        }



    }

    public function onclicksForRoundStart($id=false,$num=false){

        if(!$id){
            $id = $this->getSavedVariable('event_id');
        }

        $onclick1 = new stdClass();
        $onclick1->action = 'submit-form-content';
        $onclick1->id = 'startround'.$num.'_' . $id;
        return $onclick1;

    }




    public function playHole()
    {
    }

    public function finishHole()
    {
    }

    public function finishRound()
    {
    }

    public function finishRoundInTheEnd()
    {
    }

    
    public function getAvailability($vars){
        if(strstr($this->menuid,'startround_')) {
            return '';
        }

        return parent::getAvailability($vars);
    }


    public function getUserStatusOrCheckbox($id,$status){
        if(strstr($this->menuid,'startround_')) {
            /* checkboxes, kinda like you would find from myplaces controller */
            $this->copyAssetWithoutProcessing('checkbox-icon-checked.png');
            $this->copyAssetWithoutProcessing('checkbox-icon-unchecked.png');

            $selectstate = array('style' => 'selector_checkbox_selected_events', 'active' => '0', 'variable_value' => '1', 'allow_unselect' => 1, 'animation' => 'fade');
            return $this->getText('', array('style' => 'selector_checkbox_unselected_events', 'variable' => 'temp_selection_' . $id, 'selected_state' => $selectstate));
        }

        return parent::getUserStatusOrCheckbox($id,$status);
    }


    public function tab5()
    {

        $this->data = new stdClass();

        if($this->current_tab == '5'){
            if(strstr($this->menuid, 'showevent_')){
                $id = str_replace('showevent_','',$this->menuid);
                $this->event_id = $id;
            }

            if(strstr($this->menuid,'startround_')){
                $id = str_replace('startround_','',$this->menuid);
                $this->choosePlayers($id);
                return $this->data;
            }

            if(strstr($this->menuid,'startround2_')){
                $id = str_replace('startround2_','',$this->menuid);
                $this->startRound($id);
                return $this->data;
            }

            if(strstr($this->menuid,'paymentgate_')){
                $id = str_replace('paymentgate_','',$this->menuid);
                $this->paymentGate($id);
                return $this->data;
            }

            if(strstr($this->menuid,'inapp-')){
                $id = str_replace('inapp-','',$this->menuid);
                $this->paymentScreen($id);
                return $this->data;
            }



            if(!isset($id)){
                $id = $this->getSavedVariable('event_id');
            }

            $this->data->scroll[] = $this->getSettingsTitle('‹ {#back#}',array('onclick' => $this->getOnclick('tab1')));

            /* this covers showing the finished rounds */
            $this->initEvent();
            $ob = MobileeventparticipantsModel::model()->findByAttributes(array('play_id' => $this->current_playid,'event_id'=>$this->event_info['eventid']));

            if(is_object($ob) AND $ob->status == 'finished'){
                $this->setScoreCard();
                return $this->data;
            }

            $this->showEvent();
            $this->setTeeOffTimer();
            $buttonparams = $this->getStyleDefaultButton();

            /* overriding the footer for our use */
            $this->data->footer = array();
            if(empty($this->hole_userobj->errors)) {
                $col[] = $this->getButtonWithIcon('cancel-icon-white.png', 'notcoming_' . $id, '{#cant_make_it#}', $buttonparams, $this->getStyleDefaultText(), $this->saveAndClose('notcoming_' . $this->menuid));
                $col[] = $this->getVerticalSpacer('15');
                $buttonparams['background-color'] = '#56c656';
                if(isset($this->golf_course_purchases[$this->place_id])){
                    $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'startround_' . $id, '{#start_round#}', $buttonparams, $this->getStyleDefaultText());
                } else {
                    $col[] = $this->getButtonWithIcon('golf-ball-icon.png', 'paymentgate_' . $id, '{#start_round#}', $buttonparams, $this->getStyleDefaultText());
                }
                $this->data->footer[] = $this->getRow($col, array('text-align' => 'center'));
            }

        } else {
            $this->data->scroll[] = $this->getFullPageLoader();
        }

        return $this->data;
    }



    public function setTeeOffTimer(){
        if($this->time_today){
            $timeleft = date('U',strtotime($this->time_today)) - time();
            if($timeleft > 0){
                $col[] = $this->getImage('golf-ball-icon.png',array('height' => '30','color' => '#ffffff','margin' => '0 50 0 10'));
                $col[] = $this->getText('{#tee_off_in#} ',array('font-size' => '17','color' => '#ffffff'));
                $col[] = $this->getTimer($timeleft,array('font-size' => '17','color' => '#ffffff','font-weight' => 'bold','id' => 'teeoff'));
                $col[] = $this->getImage('golf-ball-icon.png',array('height' => '30','color' => '#ffffff','margin' => '0 10 0 50'));
                $this->data->header[] = $this->getRow($col,array('padding' => '15 10 15 10','background-color' => $this->color_topbar,'text-align' => 'center'));
            }
        }
    }

    public function showCourseOverview($id)
    {

    }

    public function displayErrors($errors){
        $this->errorState = true;

        $this->data->scroll[] = $this->getSpacer(20);
        $img[] = $this->getImage('warning-icon-md.png',array('width' => '60'));
        $this->data->scroll[] = $this->getRow($img,array('text-align' => 'center'));
        $this->data->scroll[] = $this->getText('{#sorry#}!',array('text-align' => 'center'));
        //$this->data->scroll[] = $this->getText('{#unrecoverable_error#}',array('text-align' => 'center'));
        //$this->data->scroll[] = $this->getText('place id:' .$this->place_id,array('text-align' => 'center'));

        foreach($errors as $error){
            $this->data->scroll[] = $this->getText($error,array('text-align' => 'center'));
        }

        /* this doesn't need id */
        $this->data->footer[] = $this->getTextbutton('{#cancel_this_round#}',array('id' => 'cancel_round'));
    }



}