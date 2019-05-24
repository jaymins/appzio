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

class ReportingMobilefeedbacktool extends MobilefeedbacktoolController {

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

    /* @var MobilefeedbacktoolreportsModel*/
    public $reportModel;

    public $msgcount;

    /* inbox includes the whole layout code, we call it on tab 1 to get the count */
    public $inbox;
    public $gifinfo;
    public $userstats;
    public $department_stats;

    public $cache;

    public function tab1(){
        $this->data = new stdClass();
        $this->generalInit();

        $cache = $this->getTabCache(1);

        $this->initReportsModel();
        $this->setTitle();

        if($cache){
            $this->userstats = $cache;
        } else {
            $this->userstats = $this->reportModel->userStats($this->playid);
        }

        $this->tabbing();
        $this->getTop();

        $this->statsSummary($this->userstats);
        $this->saveTabCache(1,$this->userstats);
        return $this->data;
    }

    private function getTabCache($tab){

        if($this->sessionGet('reports_tab_'.$tab)){
            $d = $this->sessionGet('reports_tab_'.$tab);

            if(time() < (int)$d['time']+3400 AND $d['count'] == count($this->msglist['all_messages'])){
                return $d['data'];
            }
        }
    }

    private function saveTabCache($tab,$data){
        $data['time'] = time();
        $data['data'] = $data;
        $data['count'] = count($this->msglist['all_messages']);

        $this->sessionSet('reports_tab_'.$tab,$data);
    }

    public function statsSummary($data){


        if(isset($data['rating_average']) AND isset($data['rating_average_count']) AND $data['rating_average'] AND $data['rating_average_count']){
            $this->getValue('thumbup.png','{#feedbacks_received#}',$data['rating_average'],$data['rating_average_count']);
            if($data['feedback_usefulness']){
                $this->getValue('thumbup.png','{#feedback_usefulness#}',$data['feedback_usefulness'],$data['feedback_usefulness_count']);
            }
        } else {
            $this->data->scroll[] = $this->getText('{#no_data_yet#}',array('margin' => '20 40 20 40','font-size' => '13','text-align' => 'center'));

        }

        if(isset($this->userlist['fundamentals']) AND is_array($this->userlist['fundamentals']) AND !empty($this->userlist['fundamentals'])){
            foreach($this->userlist['fundamentals'] AS $key=>$val){
                if(isset($data['fundamentals'][$key])){
                    $fundamental = $data['fundamentals'][$key];
                    $this->getValue('sent.jpg',$fundamental['title'],$fundamental['value'],$fundamental['count']);
                }
            }
        }

        if(isset($data['fundamentals'][0])){
            $fundamental = $data['fundamentals'][0];
            $this->getValue('sent.jpg',$fundamental['title'],$fundamental['value'],$fundamental['count']);
        }



    }

    private function setTitle(){
        $dept = $this->getSavedVariable('department_id');
        $department = isset($this->userlist['departments'][$dept]) ? $this->userlist['departments'][$dept] : 'Department';
        $name = isset($this->userlist['names_by_id'][$this->playid]['first_name']) ? $this->userlist['names_by_id'][$this->playid]['first_name'] : $this->getSavedVariable('real_name');
        $this->rewriteActionField('subject',$name .' / ' .$department);

    }

    public function tab5(){
        $this->data = new stdClass();
        $cache = $this->getTabCache(5);

        $this->generalInit();
        $this->initReportsModel();
        $this->setTitle();

        if($cache){
            $this->department_stats = $cache;
        } else {
            $this->department_stats = $this->reportModel->departmentStats($this->getSavedVariable('department_id'));
        }

        $this->tabbing();
        $this->statsSummary($this->department_stats);

        $this->saveTabCache(5,$this->department_stats);
        return $this->data;
    }

    public function tab2(){
        $this->data = new stdClass();

        $cache = $this->getTabCache(2);

        if(empty($this->userlist['subordinates'])){
            $this->data = $this->getFullPageLoader();
            return $this->data;
        }

        $this->setTitle();
        $this->tabbing();
        $this->generalInit();

        if($cache){
            $this->department_stats = $cache;
        } else {
            $this->department_stats = $this->reportModel->subordinatesStats($this->userlist['subordinates']);
        }
        $this->statsSummary($this->department_stats);
        $this->saveTabCache(2,$this->department_stats);
        return $this->data;


        $my_department = $this->getSavedVariable('department_id');
        
        if(isset($this->userlist['my_department'][$my_department])){
            $data = $this->userlist['by_department'][$my_department];
            $userdata = $this->filterUserData($data);
            $userdata = $this->userlist['subordinates'];
            $this->userlisting($userdata,4,'user_stats');
        } else {
            $this->data->scroll[] = $this->getText('{#no_permissions_for_any_additional_reports#}',array('margin' => '20 40 20 40','font-size' => '13','text-align' => 'center'));
        }

    }


    /* aka individual user */
    public function tab4(){
        $this->data = new stdClass();
        $name = false;

        $user = $this->sessionGet('user_stats');

        if(isset($this->userlist['names_by_id'][$user]['first_name'])){
            $name = $this->userlist['names_by_id'][$user]['first_name'];
        } elseif(isset($this->userlist['names'][$user]['first_name'])){
            $name = $this->userlist['names'][$user]['first_name'];
        }

        $this->tabbing($name);

        if($this->current_tab != '4'){
            $this->data->scroll[] = $this->getFullPageLoader();
            return $this->data;
        }

        $this->generalInit();
        $this->initReportsModel();
        $stats = $this->reportModel->userStats($this->sessionGet('user_stats'));
        $this->rewriteActionField('subject',$name);
        $this->getTop($this->sessionGet('user_stats'));

        $this->statsSummary($stats);

        //$this->data->scroll[] = $this->getText('hello world!');
        return $this->data;
    }

    public function initReportsModel(){
        
        if(!is_object($this->reportModel)) {
            $this->reportModel = new MobilefeedbacktoolreportsModel();
            $this->reportModel->game_id = $this->gid;
            $this->reportModel->variables = $this->vars;
            $this->reportModel->playid = $this->playid;
            $this->reportModel->userlist = $this->userlist;
            if(isset($this->userlist['fundamentals'])){
                $this->reportModel->fundamentals = $this->userlist['fundamentals'];
            }
        }

    }
    public function tabbing($name=false){
        $options['indicator_mode'] = 'bottom';

        $tabs = array('tab1' => '{#me#}');

        if(isset($this->userlist['subordinates']) AND !empty($this->userlist['subordinates'])){
            $tabs['tab2'] = '{#my_team#}';
        } else {
            return false;
        }

/*      if($this->current_tab == 4 AND $name){
            $tabs['tab4'] = $name;
        }*/

        $this->data->header[] = $this->getTabs($tabs,$options);
    }
    
    public function getValue($icon='mymail.png',$text='{#feedbacks_received#}',$rating=0.82,$num=42){

        $output[] = $this->getSpacer(15);

        $col[] = $this->getImage($icon,array('width' => '40','margin' => '0 10 0 0'));
        $col[] = $this->getText($text,array('font-size' => '16'));
        $output[] = $this->getRow($col);

        if($num < 0){
            $output[] = $this->getText('{#this_section_is_waiting_for_more_feedbacks#}',array('text-align' => 'left','font-size' => 12,'margin' => '10 0 0 0'));

        } else {
            $options['track_color'] = '#BEBEBE';

            if($rating < 0.4){
                $options['progress_color'] = '#FF2726';
            } elseif($rating > 0.6){
                $options['progress_color'] = '#42C840';
            } else {
                $options['progress_color'] = '#E0A729';
            }

            $options['style'] = 'status_progress_report_green';
            $options['animate'] = '1';
            $output[] = $this->getProgress($rating,$options);

            unset($col);
            $col[] = $this->getText($num .' {#feedbacks#}',array('font-size' => '13'));
            $col[] = $this->getText(round($rating*100,0) .'%',array('font-size' => '13','float' => 'right', 'floating' => 1));
            $output[] = $this->getRow($col);
        }

        $this->data->scroll[] = $this->getColumn($output,array('margin' => '0 30 0 30'));
        $this->data->scroll[] = $this->getSpacer(15);
        $this->data->scroll[] = $this->getText('',array('height' => 1,'width' => '100%','background-color' => '#BEBEBE'));
        
        return true;
    }



    public function getTop($playid=false){
        $pic = false;

        if($playid){
            $vars = AeplayVariable::getArrayOfPlayvariables($playid);
            if(isset($vars['profilepic']) AND $vars['profilepic']){
                $pic = $vars['profilepic'];
            }
        } else {
            $pic = $this->getSavedVariable('profilepic');
        }

        if(!$pic){
            $pic = 'profile-image-placeholder.png';
        }

        if(isset($this->userstats['feedbacks_sent'])){
            $string = $this->localizationComponent->smartLocalize('{#feedbacks_sent#}');
            $left[] = $this->getText($this->userstats['feedbacks_sent'],array('font-size' => '38','text-align' => 'center'));
            $left[] = $this->getText(strtoupper($string),array('font-size' => '12','text-align' => 'center'));
            $row[] = $this->getColumn($left,array('width' => '80','text-align' => 'center','vertical-align' => 'middle'));
        }

        $row[] = $this->getVerticalSpacer('10');
        /* column 2 */

        if($playid){
            $col1[] = $this->getImage($pic,array('width' => '100','crop' => 'round','border-width' => '5','border-color' => '#e8e8e8',
                'border-radius' => '50','margin' => '0 0 10 0'));
        } else {
            $col1[] = $this->getImage($pic,array('width' => '100','crop' => 'round','border-width' => '5','border-color' => '#e8e8e8',
                'onclick' => $this->getOnclick('action',true,$this->getActionidByPermaname('profile')),
                'border-radius' => '50','margin' => '0 0 10 0'));
        }


        $row[] = $this->getColumn($col1,array('text-align' => 'center','width' => '33%'));
        $row[] = $this->getVerticalSpacer('10');

        if(isset($this->userstats['feedbacks_received'])){
            $string = $this->localizationComponent->smartLocalize('{#feedbacks_received#}');
            $right[] = $this->getText($this->userstats['feedbacks_received'],array('font-size' => '38','text-align' => 'center'));
            $right[] = $this->getText(strtoupper($string),array('font-size' => '12','text-align' => 'center'));
            $row[] = $this->getColumn($right,array('width' => '80','text-align' => 'center','vertical-align' => 'middle'));
        }

        $this->data->header[] = $this->getRow($row,array('text-align' => 'center','padding' => '10 0 0 0',
            'shadow-color' => '#33000000','shadow-radius' => '3','shadow-offset' => '0 0',
            'vertical-align' => 'middle',
            'height' => '130',
            'background-color' => '#ffffff','margin' => '0 0 10 0'
        ));
    }


    public function getColLeft(){
        $col2[] = $this->getImage('big-icon-points.png',array('height' => '30','opacity' => '0.7'));
        $inside[] = $this->getText($this->points,array('font-size' => '16','color' => '#a0a0a0'));
        $inside[] = $this->getText('pt',array('font-size' => '15','color' => '#a0a0a0'));
        $col2[] = $this->getRow($inside,array('height' => '30','text-align' => 'center','vertical-align' => 'middle','padding' => '5 5 5 5'));



        /* column 1 */
        return $this->getRow($col2,array(
            'text-align' => 'left','vertical-align' => 'middle','padding' => '0 10 0 6',
            'height' => '40', 'background-color' => '#e8e8e8','border-radius' => '20','width' => $this->screen_width/3.6,
            'onclick' => $this->getOnclick('action',true,$this->getActionidByPermaname('toplist'))));

    }

    public function getColRight(){

        $rank = $this->getSavedVariable('primary_points_rank');

        if($rank == 1){
            $nm = 'st';
        } elseif($rank == 2){
            $nm = 'nd';
        } elseif($rank == 3) {
            $nm = 'rd';
        } else {
            $nm = 'th';
        }

        $inside[] = $this->getText($rank,array('font-size' => '16','color' => '#a0a0a0'));
        $inside[] = $this->getText($nm,array('font-size' => '15','color' => '#a0a0a0'));
        $col2[] = $this->getRow($inside,array('height' => '30','text-align' => 'center','vertical-align' => 'middle','padding' => '5 5 5 5'));
        $col2[] = $this->getImage('big-icon-trophy.png',array('height' => '30','opacity' => '0.7'));

        /* column 1 */
        return $this->getRow($col2,array(
            'text-align' => 'right','vertical-align' => 'middle','padding' => '0 6 0 10',
            'height' => '40', 'background-color' => '#e8e8e8','border-radius' => '20','width' => $this->screen_width/3.6,
            'onclick' => $this->getOnclick('action',true,$this->getActionidByPermaname('toplist'))
        ));
    }



}