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

class MobilegateController extends ArticleController {

    public $data;
    public $points;
    public $secondary_points;

    public function tab1(){

        if(isset($this->menuid) AND $this->menuid == 'deduct-1') {
            $obj = Aeaction::model()->findByPk($this->action_id);
            $points = $obj->points;
            $deduct = 1 + $points;
            $aeplaction = AeplayAction::model()->findByPk($this->actionid);
            $aeplaction->points = $aeplaction->points - $deduct;
            $this->closeGate();
            $aeplaction->update();
        }elseif(isset($this->menuid) AND $this->menuid == 'deduct-0'){
            $obj = Aeaction::model()->findByPk($this->action_id);
            $points = $obj->points;
            $deduct = $points;
            $aeplaction = AeplayAction::model()->findByPk($this->actionid);
            $aeplaction->points = -$deduct;
            $this->closeGate();
            $aeplaction->update();
        } elseif(isset($this->menuid) AND $this->menuid == 'reset-branch'){
            $sql = "DELETE ae_game_play_action FROM ae_game_play_action
                         LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                         WHERE ae_game_play_action.play_id = :playId
                         AND ae_game_branch_action.branch_id = :branchId";

            Yii::app()->db
                ->createCommand($sql)
                ->bindValues(array(':branchId' => $this->branchobj->id, ':playId' => $this->playid))
                ->query();
            $this->closeGate();
            
            Appcaching::setGlobalCache($this->playid.'-listbranches',true);
        }

        $this->data = new StdClass();
        $this->points = Aeplay::getUserPlayPoints($this->playid);
        $this->secondary_points = Aeplay::getUserSecondaryPoints($this->playid);

        Yii::import('application.modules.aelogic.packages.actionMobilequiz.models.*');
        $branchstats = MobilequizModel::getBranchInfo($this->playid,$this->branchobj->id,'secondary');

        if($this->points > 5){
            $num = 5;
        } elseif($this->points < 0) {
            $num = 0;
        } else {
            $num = $this->points;
        }


        $cols[] = $this->getSpacer('15');
        $cols[] = $this->getBranchProgress($branchstats);
        $cols[] = $this->getSpacer('15');

        $pattern = $this->getImageFileName('pattern.png');

        $this->data->footer[] = $this->getColumn($cols,array());

        $varname = $this->branchobj->id .'_status';
        $data = $this->getSavedVariable($varname);
        $gate_open = false;

        if($data){
            $data = json_decode($data);
            if(isset($data->gate_open)){
                $gate_open = $data->gate_open;
            }
        }


        if($branchstats['provess'] == 1){

            $this->data->scroll[] = $this->getBanner(false,array('ad_size' => 'rectangle'));
            $this->data->scroll[] = $this->getText('Loistavaa, 100% tasosta oikein! Voit jatkaa pelaamista menettämättä energiaa.',array('style' => 'quiz_hinttext'));

            $one['action'] = 'submit-form-content';
            $one['id'] = 'deduct-0';
            $two['action'] = 'complete-action';
            $btn = array($one,$two);

            $this->data->scroll[] = $this->getText('Jatka tästä pelaamista',array('id' => 'load','style' => 'quizz_button','onclick' => $btn));
        } elseif(is_numeric($gate_open) AND $gate_open < time()){
            $this->data->scroll[] = $this->getBanner(false,array('ad_size' => 'rectangle'));

            $obj = Aeaction::model()->findByPk($this->action_id);
            $points = $obj->points;
            $num = $points < 6 ? $points : 5;

            $row[] = $this->getImage('battery_icon_' .$num .'.png',array('align' => 'center', 'width' => '70'));
            $row[] = $this->getText('Lataus valmiina. Saat +' .$points .' lisäenergian, kun jatkat pelaamista',array('style' => 'quiz_hinttext_left'));
            $this->data->scroll[] = $this->getRow($row,array('text-align' => 'center'));

            $this->data->scroll[] = $this->getTextbutton('Jatka Pelaamista',array('id' => 'load','style' => 'quizz_button','action' => 'complete-action'));

        } elseif(isset($this->menuid) AND $this->menuid == 'load'){

            $time = $this->doLoadRequest();
            $time = $time - time();

            $this->data->footer[] = $this->getBanner(false);
            $this->data->scroll[] = $this->getSpacer('30');

            $row[] = $this->getImage('battery_icon_' .$num .'.png',array('align' => 'center', 'width' => '70'));
            $this->data->scroll[] = $this->getRow($row,array('text-align' => 'center'));


            $time = round($time/60,0);
            $time = $this->convertToHoursMins($time);





            $row2[] = $this->getText('Aikaa jäljellä '.$time,array('style' => 'quiz_hinttext'));



            $row2[] = $this->getText('100% tasosta oikein = lisää virtaa',array('style' => 'quiz_hinttext'));
            $this->data->scroll[] = $this->getColumn($row2,array('text-align' => 'center'));

            $this->data->scroll[] = $this->getTextbutton('Päivitä ...',array('id' => 'load','style' => 'quizz_button'));

            $one['action'] = 'submit-form-content';
            $one['id'] = 'reset-branch';
            $two['action'] = 'list-branches';
            $three['action'] = 'go-home';
            $btn2 = array($three,$one,$two);

            $this->data->scroll[] = $this->getText('Aloita taso alusta',array('id' => 'start-over','style' => 'quizz_button','onclick' => $btn2));

        } else {
            $this->data->scroll[] = $this->getBanner(false,array('ad_size' => 'rectangle'));

            $row[] = $this->getImage('battery_icon_' .$num .'.png',array('align' => 'center', 'width' => '70'));
            $this->data->scroll[] = $this->getRow($row,array('text-align' => 'center'));
            $this->data->scroll[] = $this->getTextbutton('Lataa lisää virtaa (odota 6h)',array('id' => 'load','style' => 'quizz_button'));
        }


        $one['action'] = 'submit-form-content';
        $one['id'] = 'deduct-1';
        $two['action'] = 'complete-action';

        $onclick = array($one,$two);

        if($branchstats['provess'] != 1 AND $this->points > 0) {
            $this->data->scroll[] = $this->getText('Jatka heti (-1 energia)', array('style' => 'quizz_button', 'onclick' => $onclick));
        }

        $this->data->scroll[] = $this->getTextbutton('Päävalikkoon',array('action' => 'go-home','sync_open'=>'1','id' => 'load','style' => 'quizz_button'));


        /*        $col1[] = $this->getText($this->points,array('style' => 'quiz_battery'));
                $bg1 = $this->getImageFileName('battery_' .$num .'.png');
                $row[] = $this->getColumn($col1,array('background-image' => $bg1,'background-size'=>'contain','width' => '153','height' => '68'));

                $col2[] = $this->getText($this->secondary_points,array('style' => 'quiz_points'));
                $bg2 = $this->getImageFileName('points.png');
                $row[] = $this->getColumn($col2,array('background-image' => $bg2,'width' => '167','background-size'=>'contain','height' => '68'));

                $this->data->scroll[] = $this->getRow($row,array('width' => '640','margin' => '30 0 10 0'));

                $toplist = $this->getConfigParam('toplist_branch');
                $this->data->scroll[] = $this->getImagebutton('btn-parhaat.png','toplist',false,array('action' => 'open-branch', 'config' => $toplist));
                $this->data->scroll[] = $this->getImagebutton('btn-kuukausi.png','toplist',false,array('action' => 'open-branch', 'config' => $toplist));
                $this->data->scroll[] = $this->getBanner('btn-kuukausi.png',array('margin' => '5 0 0 0'));*/
        return $this->data;
    }


    public function closeGate(){
        $varname = $this->branchobj->id .'_status';
        AeplayVariable::deleteWithName($this->playid,$varname,$this->gid);
    }

    public function doLoadRequest(){

        $varname = $this->branchobj->id .'_status';
        $data = $this->getSavedVariable($varname);

        if(!isset($this->vars[$varname])){
            Aevariable::addGameVariable($this->gid,$varname);
        }

        if(!$data){
            $timetowait = $this->getConfigParam('time_to_wait');
            $data['gate_open'] = time() + $timetowait*60;
            $this->saveVariable($varname,json_encode($data));
            return $data['gate_open'];
        }

        $data = json_decode($data);
        if(isset($data->gate_open)){
            return $data->gate_open;
        } else {
            return time();
        }

    }


    public function convertToHoursMins($time, $format = '%02d:%02d') {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }




    public function getBranchProgress($branchstats){

        $options['track_color'] = '#aeaeae';
        $options['progress_color'] = '#e6870f';
        $options['text_content'] = round($branchstats['progress']*100,0) .'% tehtynä';
        $options['style'] = 'status_progress_1';
        $row[] = $this->getProgress($branchstats['progress'],$options);

        $options['style'] = 'status_progress_2';
        $options['progress_color'] = '#3aa735';
        $options['text_content'] = round($branchstats['provess']*100,0) .'% oikein';

        $row[] = $this->getProgress($branchstats['provess'],$options);
        $col[] = $this->getRow($row,array('width' => '100%','text-align' => 'center'));
        $output = $this->getColumn($col);

        return $output;

    }



}