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

class MobilerewardingController extends ArticleController {

    public $data;
    public $points;
    public $secondary_points;

    public function tab1(){

        $this->data = new StdClass();

        $this->data->footer[] = $this->getBanner(false);

        $this->points = Aeplay::getUserPlayPoints($this->playid);
        $this->secondary_points = Aeplay::getUserSecondaryPoints($this->playid);

        if($this->points > 5){
            $num = 5;
        } elseif($this->points < 0) {
            $num = 0;
        } else {
            $num = $this->points;
        }

        $row[] = $this->getImage('battery_icon_' .$num .'.png',array('align' => 'center', 'width' => '70'));
        $row[] = $this->getText($this->points,array('font-size' => '22'));
        $this->data->scroll[] = $this->getRow($row,array('text-align' => 'center'));

        $this->rewardBlock('daily',3,'Päiväpalkinto','Saat aina 12 tunnin välein lisää energiaa',1,false,true);

        if($this->getSavedVariable('real_name')){
            $this->rewardBlock('toplist',2,'Toplistan avaaminen','Aktivoi toplista päävalikosta antamalla pelaajanimesi.');
        } else {
            $this->rewardBlock('toplist',2,'Toplistan avaaminen','Aktivoi toplista päävalikosta antamalla pelaajanimesi.',false,true);
        }

        if($this->getSavedVariable('email')){
            $this->rewardBlock('raffle',2,'Arvontojen aktivoiminen','Aktivoi osallistumisesti kuukausiarvontoihin päävalikosta.');
        } else {
            $this->rewardBlock('raffle',2,'Arvontojen aktivoiminen','Aktivoi osallistumisesti kuukausiarvontoihin päävalikosta.',false,true);
        }

        $this->rewardBlock('100points',4,'Kerää 100 pistettä','Tarvitset viisikymmentä oikeaa vastausta kerätäksesi tarvittavat 100 pistettä.',$this->secondary_points/100);
/*        $this->rewardBlock('weekly',5,'viikkopalkinto','Pelaamalla ainakin neljänä päivänä viikossa, saat aktivoitua viikkopalkinnon',1,true);*/


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

    public function claimPoints($points,$id,$time=false){

        $awards = $this->getSavedVariable('awards');

        if($awards){
            $awards = json_decode($awards,true);
        } else {
            $awards = array();
        }

        if($time){
            $awards[$id] = time() + (12 * 60 * 60);
        } else {
            $awards[$id] = $points;
        }

        AeplayVariable::updateWithName($this->playid,'awards',json_encode($awards),$this->gid,$this->userid);
        Aeplay::addSubtractPoints($this->playid,'primary',$points,$this->gid);
        $this->loadVariableContent(true);


    }

    public function rewardBlock($id,$points,$title,$description,$progress=1,$locked=false,$timer=false){

        if(isset($this->menuid) AND strstr($this->menuid,'claim_') AND $this->menuid == 'claim_'.$id){
            $this->claimPoints($points,$id,$timer);
            $act['action'] = 'submit-form-content';
            //$this->data->onload[] = $act;
        }

        $awards = $this->getSavedVariable('awards');
        $awards = json_decode($awards,true);

        $container1[] = $this->getImage('battery_icon_' .$points .'.png',array('align' => 'center', 'width' => '40','margin' => '8 8 8 8'));
        $container2[] = $this->getText('LUNASTA',array('id' => $id,'style' => 'reward_button_text'));
        $cols[] = $this->getRow($container1);
        $cols[] = $this->getText($description, array('style' => 'reward_description'));

        if($timer){

            if(isset($awards[$id])){
                if($awards[$id] < time()){
                    $onclick = new StdClass();
                    $onclick->action = 'submit-form-content';
                    $onclick->id = 'claim_' .$id;
                    $onclick2 = new StdClass();
                    $onclick2->action = 'open-interstitial';
                    $cols[] = $this->getRow($container2,array('style' => 'rewarding_claim_button','onclick' => array($onclick,$onclick2)));
                } else {
                    $comparison = 12 * 60 * 60;
                    $left = $awards[$id] - time();

                    $h = $left / 3600 % 24;
                    $m = $left / 60 % 60;
                    $s = $left % 60;

                    if(strlen($h) == 1){ $h = '0'.$h; }
                    if(strlen($m) == 1){ $m = '0'.$m; }
                    if(strlen($s) == 1){ $s = '0'.$s; }

                    $options['style'] = 'award_progress';
                    $options['progress_color'] = '#44d939';
                    $options['children-style'] = new StdClass();
                    $options['text_content'] = $h .':' .$m;
                    $options['track_color'] = '#d5d5d5';
                    $cols[] = $this->getProgress(1-($comparison/$left),$options);
                }
            } else {
                $onclick = new StdClass();
                $onclick->action = 'submit-form-content';
                $onclick->id = 'claim_' .$id;
                $onclick2 = new StdClass();
                $onclick2->action = 'open-interstitial';

                $cols[] = $this->getRow($container2,array('style' => 'rewarding_claim_button','onclick' => array($onclick,$onclick2)));

            }

        }elseif($locked){
            $container3[] = $this->getImage('lock-icon.png',array('align' => 'center', 'width' => '40'));
            $cols[] = $this->getRow($container3,array('width' => '90','margin' => '8 8 8 8','text-align' => 'center'));
        } elseif($progress < 1){
            $options['style'] = 'award_progress';
            $options['progress_color'] = '#44d939';
            $options['children-style'] = new StdClass();
            $options['text_content'] = round($progress*100,0) .'%';
            $options['track_color'] = '#d5d5d5';
            $options['width'] = 90;
            $cols[] = $this->getProgress($progress,$options);
        } else {
            if(isset($awards[$id])){
                $container5[] = $this->getText('LUNASTETTU',array('id' => $id,'style' => 'reward_button_text'));
                $cols[] = $this->getRow($container5,array('style' => 'award_progress'));
            } else {
                $onclick = new StdClass();
                $onclick->action = 'submit-form-content';
                $onclick->id = 'claim_' .$id;

                $onclick2 = new StdClass();
                $onclick2->action = 'open-interstitial';

                $cols[] = $this->getRow($container2,array('style' => 'rewarding_claim_button','onclick' => array($onclick,$onclick2)));
            }
        }

        $row[] = $this->getText($title .' +' .$points,array('style' => 'rewarding_title'));
        $row[] = $this->getRow($cols,array('vertical-align' => 'middle'));
        $this->data->scroll[] = $this->getColumn($row,array('style' => 'rewarding_row'));


    }





}