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

class MobilequizController extends ArticleController {

    public $data;
    public $points;
    public $secondary_points;

    public function tab1(){


/*      $cachepointer = $this->playid.'-listbranches';
        $cache = Appcaching::getGlobalCache($cachepointer);

        $this->data = new StdClass();

        if($cache){
            $onload['action'] = 'list-branches';
            $this->data->onload[] = $onload;
            Appcaching::removeGlobalCache($cachepointer);
        }*/


        $this->points = Aeplay::getUserPlayPoints($this->playid);
        $this->secondary_points = Aeplay::getUserSecondaryPoints($this->playid);

        /* add the daily energy */
        if($this->points < 3){
            $time = $this->getSavedVariable('dailyenergy');

            if(!$time){
                $this->saveVariable('dailyenergy',time());
            } else {
                if($time + 86400 < time()){
                    $howmany = 3 - $this->points;
                    Aeplay::addSubtractPoints($this->playid,'primary',$howmany,$this->gid);
                    $this->saveVariable('dailyenergy',time());
                }
            }
        }

        $img = $this->getImageFileName('quiz-logo.png');
        $this->data->scroll[] = $this->getImage($img);

        $this->getSwiper();

        if($this->points > 5){
            $num = 5;
        } elseif($this->points < 0) {
            $num = 0;
        } else {
            $num = $this->points;
        }

        $reward_branch = $this->getConfigParam('reward_branch');

        $onclick = new StdClass();
        $onclick->action = 'open-branch';
        $onclick->state = 'active';
        $onclick->sync_open = 1;
        $onclick->sync_close = 1;
        $onclick->action_config = $reward_branch;

        $col1[] = $this->getText($this->points,array('style' => 'quiz_battery'));
        $bg1 = $this->getImageFileName('main-battery-' .$num .'.png');
        $row[] = $this->getColumn($col1,array('background-image' => $bg1,'background-size'=>'contain','width' => '50%','height' => '68','onclick' => $onclick));

        $col2[] = $this->getText($this->secondary_points,array('style' => 'quiz_points'));
        $bg2 = $this->getImageFileName('main-points.png');
        $row[] = $this->getColumn($col2,array('background-image' => $bg2,'width' => '50%','background-size'=>'contain','height' => '68'));
        $this->data->scroll[] = $this->getRow($row,array('width' => '100%','margin' => '0 0 0 0'));

        $toplist = $this->getConfigParam('toplist_branch');


        $this->data->scroll[] = $this->getTextbutton('Parhaat Pelaajat',array('action' => 'open-branch', 'config' => $toplist,'id' => 'parhaat','style' => 'quizz_button'));

        if($this->getConfigParam('raffle_active')){
            $raffle = $this->getConfigParam('raffle_branch');
            $this->data->scroll[] = $this->getTextbutton('Kuukausiarvonta',array('action' => 'open-branch', 'config' => $raffle,'id' => 'kuukausi','style' => 'quizz_button'));
        }

        //$this->data->footer[] = $this->getAwardTimer();
        $this->data->footer[] = $this->getBanner(false,array('margin' => '0 0 0 0'));
        //$this->data->onload[]['action'] = 'list-branches';
        return $this->data;
    }


    public function getAwardTimer(){
        $time = json_decode($this->getSavedVariable('awards'),true);

        if(is_array($time) AND !empty($time) AND isset($time['daily'])){
            $comparison = 12 * 60 * 60;
            $left = $time['daily'] - time();

            $left = -60;

            $h = $left / 3600 % 24;
            $m = $left / 60 % 60;
            $s = $left % 60;

            if(strlen($h) == 1){ $h = '0'.$h; }
            if(strlen($m) == 1){ $m = '0'.$m; }
            if(strlen($s) == 1){ $s = '0'.$s; }

            //$options['style'] = 'award_progress';
            $options['progress_color'] = '#44d939';
            $options['children-style'] = new StdClass();
            $options['text_content'] = $h .':' .$m .':' .$s;
            $options['text-align'] = 'center';
            $options['track_color'] = '#d5d5d5';
            $options['floating'] = 1;
            $options['float'] = 'center';
            $options['mode'] = 'countdown';
            return $this->getTimer($left,$options);
        }

    }

    public function getSwiper(){

        $branches = Aebranch::model()->findAllByAttributes(array('game_id' => $this->gid));

        $this->loadBranchList();

        foreach($branches as $branch){
            if(strstr($branch->name,'game_')){
                $name = str_replace('game_','',$branch->name);
                $num = substr($name,0,strpos($name,'_'));
                $name = substr($name,strpos($name,'_')+1);
                $conf = json_decode($branch->config);

                if(isset($conf->title_on_mainmenu) AND $conf->title_on_mainmenu){
                    $requires = $conf->title_on_mainmenu;
                } else {
                    $requires = 0;
                }

                if(isset($this->available_branches[$branch->id])){
                    $games[$num] = array('id' => $branch->id,'name' => $name ,'points'=>$requires);
                }
            }
        }

        if(!isset($games)){
            return false;
        }

        foreach($games as $game){
            $swipes[] = $this->getOneSwipe($game);
        }

        $this->data->scroll[] = $this->getSpacer('10');
        if(isset($swipes)){
            $this->data->scroll[] = $this->getSwipearea($swipes,array('width' => '100%','animate' => 'nudge','remember_position' => "1"));
        } else {
            $this->data->scroll[] = $this->getText('Ei tehtäviä. :(',array('height' => '245'));
        }
    }

    public function getOneSwipe($game){
        $bgimage = $this->getImageFileName('scrollarea.png');

        if($this->secondary_points < $game['points']){
            $col[] = $this->getText($game['name'],array('style' => 'quiz_title_text'));
            $col[] = $this->getImage('lock-icon.png',array('style' => 'lock_icon'));
            $col[] = $this->getText('Tarvitset ' .$game['points'] .' pistettä
pelataksesi tämän tason',array('style' => 'quiz_lock_title'));
            $output = $this->getColumn($col,array('text-align' => 'center','align' => 'center','width' => $this->screen_width,'height' => $this->screen_width*0.7, 'vertical-align' => 'top','background-image' => $bgimage,'background-size'=>'cover','id' => $game['id']));
        } else {

            $branchstats = Aeplay::getUserBranchStats($this->playid,$game['id'],'secondary');

            if($branchstats['completed_actions_with_points'] == 0 OR $branchstats['totalactions_with_points'] == 0){
                $progress = 0;
            } else {
                $progress = $branchstats['completed_actions_with_points'] / $branchstats['totalactions_with_points'];
            }

            if($branchstats['points'] == 0 OR $branchstats['max_possible_points_so_far'] == 0){
                $provess = 0;
            } else {
                $provess = $branchstats['points'] / $branchstats['max_possible_points_so_far'];
            }

            $btn = $this->getImageFileName('pelaa-btn.png');
            $options['track_color'] = '#aeaeae';
            $options['progress_color'] = '#e6870f';
            $options['text_content'] = round($progress*100,0) .'% tehtynä';
            $options['style'] = 'status_progress_1';
            $col[] = $this->getText($game['name'],array('style' => 'quiz_title_text'));

            $col[] = $this->getImagebutton('pelaa-btn.png','toplist',false,array('action' => 'open-branch', 'sync_open'=>1,'sync_close'=>1,'config' => $game['id'],'style' => 'quiz_playbtn'));
            $row[] = $this->getProgress($progress,$options);

            $options['style'] = 'status_progress_2';
            $options['progress_color'] = '#3aa735';
            $options['text_content'] = round($provess*100,0) .'% oikein';
            $options['track_color'] = '#aeaeae';

            $row[] = $this->getProgress($provess,$options);
            $col[] = $this->getRow($row,array('width' => '100%','text-align' => 'center'));
            $col[] = $this->getSpacer(20);

            $output = $this->getColumn($col,array('width' => $this->screen_width,'height' => round($this->screen_width*0.7,0), 'vertical-align' => 'top','background-image' => $bgimage,'background-size'=>'cover','id' => $game['id']));
        }




        return $output;

    }



}