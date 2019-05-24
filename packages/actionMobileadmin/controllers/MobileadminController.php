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
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');
Yii::import('application.modules.aelogic.packages.actionMobileadmin.models.*');

class MobileadminController extends ArticleController {

    public $data;
    public $lightcolor;
    public $paging = 100;

    public function tab1(){
        $this->setHeader();
        $colorhelp = new Color($this->color_topbar);
        $this->lightcolor = $colorhelp->lighten(35);
        $this->hideUnhide();
        $this->setStatsView();
        return $this->data;
    }

    public function hideUnhide(){
        if(strstr($this->menuid,'unhide-')){
            $id = str_replace('unhide-','',$this->menuid);
            AeplayVariable::updateWithName($id,'hide_user','0',$this->gid);
        } elseif(strstr($this->menuid,'hide-')){
            $id = str_replace('hide-','',$this->menuid);
            AeplayVariable::updateWithName($id,'hide_user','1',$this->gid);
        }
    }


    public function getPaging($count,$granularity){
        $page = $this->getSavedVariable('admin_paging') ? $this->getSavedVariable('admin_paging') : 0;

        $col[] = $this->getTextbutton('|‹‹',array('width' => '12%','id' => 'page_start'));
        $col[] = $this->getVerticalSpacer('2%');
        $col[] = $this->getTextbutton('‹',array('width' => '12%','id' => 'page_minus'));
        $txt = '{#records#} '.$page .' - '.($page+$granularity) .' of '.$count;
        $col[] = $this->getText($txt,array('width' => '48%','font-size' => '11', 'text-align' => 'center'));
        $col[] = $this->getTextbutton('› ',array('width' => '12%', 'id' => 'page_plus'));
        $col[] = $this->getVerticalSpacer('2%');
        $col[] = $this->getTextbutton('››| ',array('width' => '12%', 'id' => 'page_end'));

        $this->data->footer[] = $this->getSpacer(5);
        $this->data->footer[] = $this->getRow($col);

    }

    public function tab2(){
        $this->data = new StdClass();
        $this->setHeader();
        $granularity = $this->paging;

        if($this->current_tab == '2'){
            if($this->userActions()){

                $count = Aeplay::model()->countByAttributes(array('game_id' => $this->gid));
                $varvalue = $this->getSavedVariable('admin_paging')?$this->getSavedVariable('admin_paging'):0;

                switch($this->menuid){
                    case 'page_minus':
                        $varvalue = $varvalue - $granularity;
                        break;

                    case 'page_plus':
                        $varvalue = $varvalue + $granularity;
                        break;

                    case 'page_start':
                        $varvalue = 0;
                        break;

                    case 'page_end':
                        $varvalue = $count-$granularity;
                        break;
                }

                $this->saveVariable('admin_paging',$varvalue);

                if($count > $granularity){
                    $this->getPaging($count,$granularity);
                }

                if($varvalue < 0){
                    $varvalue = 0;
                }

                $limitquery = $varvalue .','.$granularity;
                $results = MobileadminModel::searchVariables(false,$this->gid,$limitquery);
                $this->userListingWithControls($results);
            }
        } else {
            $this->data->scroll[] = $this->getSpacer(50);
            $this->data->scroll[] = $this->getLoader('Loading...',array('color' => '#000000'));
        }

        return $this->data;
    }

    /* returns false, if not supposed to continue with the flow */
    public function userActions(){
        if(strstr($this->menuid,'openuser-')){
            $id = str_replace('openuser-','',$this->menuid);
            $this->showUser($id);
            return false;
        }

        if(strstr($this->menuid,'deluser-')){
            $id = str_replace('deluser-','',$this->menuid);
            $this->showUser($id,true);
            return false;
        }

        if(strstr($this->menuid,'delete-user-')){
            Yii::import('userGroups.models.UserGroupsUseradmin');
            $id = str_replace('delete-user-','',$this->menuid);
            $play = Aeplay::model()->findByPk($id);

            if(isset($play->user_id)){
                $user = $play->user_id;
                UserGroupsUseradmin::model()->deleteByPk($user);
            }

            return true;
        }

        return true;
    }

    public function tab3(){
        $this->setHeader();

        if($this->userActions()) {
            $results = MobileadminModel::getFlaggedUsers($this->gid);

            if (empty($results)) {
                $this->data->scroll[] = $this->getSpacer('30');
                $this->data->scroll[] = $this->getText('{#no_flagged_users#}', array('font-size' => '12', 'text-align' => 'center'));
                return $this->data;
            }

            $this->userListingWithControls($results);
        }

        return $this->data;
    }


    public function tab4(){
        $this->setHeader();
        if($this->userActions()) {
            $this->searchBar();
        }
        return $this->data;
    }


    private function setHeader(){
        $this->data = new StdClass();
        $this->initMobileMatching();
        $this->data->header[] = $this->getTabs(array('tab1' => 'Stats','tab2' => 'Users','tab3' => 'Flagged', 'tab4' => 'Search'),false,false,false);
    }


    public function searchBar(){

        $value = $this->getSubmitVariable('searchterm') ? $this->getSubmitVariable('searchterm') : '';

        $row[] = $this->getImage('search-icon-for-field.png',array('height' => '25'));
        $row[] = $this->getFieldtext($value,array('style' => 'admin_searchbox_text','hint' => '{#free_text_search#}','id' => 'searchbox','variable' => 'searchterm'));
        $col[] = $this->getRow($row,array('style' => 'admin_searchbox'));

        $col[] = $this->getTextbutton('Search',array('style' => 'admin_searchbtn','id' => 'dosearch'));
        $this->data->header[] = $this->getRow($col,array('background-color' => $this->color_topbar));
        $this->data->scroll[] = $this->getText('');

        if($this->menuid == 'dosearch'){
            $this->searchFilters();
            $this->searchResults();
        }
    }



    public function showUser($id,$delete=false){

        $back = new StdClass();
        $back->action ='open-tab';
        $back->action_config = '2';

        $dodelete = new StdClass();
        $dodelete->action ='submit-form-content';
        $dodelete->id = 'delete-user-'.$id;

        if($delete === true){
            $col[] = $this->getText('‹ back',array('onclick' => $back,'style' => 'general_admin_text'));
            $col[] = $this->getText('Are you sure you want to delete this user? This action can\'t be undone.',array('onclick' => $back,'style' => 'general_admin_text'));
            $col[] = $this->getText('Yes, Delete',array('onclick' => $dodelete,'style' => 'general_button_style_red'));
            $this->data->scroll[] = $this->getColumn($col,array('background-color' => '#ffb8b8','margin' => '0 0 10 0'));
            unset($col);
        } else {
            $this->data->scroll[] = $this->getText('‹ back',array('onclick' => $back,'style' => 'general_admin_text'));
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($id);

        if(isset($vars['profilepic'])){
            $img[] = $this->getImage($vars['profilepic'],array('crop' => 'round','width' => '80', 'height' => '80','imgwidth' => '160', 'imgheight' => '160'));
            $this->data->scroll[] = $this->getRow($img,array('text-align' => 'center'));
        }

        $count = 0;

        $phase = ( isset($vars['reg_phase']) ? $vars['reg_phase'] : '' );
        if ( $phase == 'complete' ) {
            $this->data->scroll[] = $this->getText( 'User active', array( 'style' => 'notification-active' ) );
        } else {
            $this->data->scroll[] = $this->getText( 'Not active', array( 'style' => 'notification-inactive' ) );
        }

        foreach($vars as $key=>$var){
            $col[] = $this->getText($key,array('font-size' => '10','margin' => '5 5 5 15','width' => '100'));
            $col[] = $this->getText($var,array('font-size' => '10','margin' => '5 15 5 5','text-align' => 'left'));
            $this->data->scroll[] = $this->getRow($col);
            unset($col);
            if($count == 80){
                break;
            }
            $count++;
        }

        if($count == '80'){
            $this->data->scroll[] = $this->getText('Showing only 80 results', array('style' => 'general_admin_text'));
        }

        if($delete === false) {
            $this->data->scroll[] = $this->getText('Delete User', array('onclick' => $this->deluserLink($id), 'style' => 'general_button_style_red'));
        }

    }

    public function searchResults(){

        $searchterm = $this->getSubmitVariable('searchterm') ? $this->getSubmitVariable('searchterm') : '';
        $results = MobileadminModel::searchVariables($searchterm,$this->gid);

        $this->userListingVariables($results);
    }

    public function userListingWithControls($results){

        $hidephotos = $this->getSavedVariable('admin_hide_photos') ? $this->getSavedVariable('admin_hide_photos') : 0;
        $hide_users_without_names = ( $this->getConfigParam( 'hide_users_without_names' ) ? true : false );

        if($this->menuid == 'hide_photos'){
            $this->saveVariable('admin_hide_photos',1);
            $hidephotos = 1;
        }

        if($this->menuid == 'show_photos'){
            $this->saveVariable('admin_hide_photos',0);
            $hidephotos = 0;
        }

        if($hidephotos){
            $onclick = new StdClass();
            $onclick->action = 'submit-form-content';
            $onclick->id = 'show_photos';
            $col[] = $this->getImage('hide-icon.jpg',array('style' => 'admin_grid_header_icon','onclick' => $onclick));
        } else {
            $onclick = new StdClass();
            $onclick->action = 'submit-form-content';
            $onclick->id = 'hide_photos';
            $col[] = $this->getImage('visible-icon.png',array('style' => 'admin_grid_header_icon','onclick' => $onclick));
        }
        
        $col[] = $this->getText('{#name#}',array('style' => 'admin_grid_header'));
        $col[] = $this->getText('{#flags#}',array('style' => 'admin_grid_header_photo'));
        if(!$hidephotos){
            $col[] = $this->getVerticalSpacer('8');
        }
        $col[] = $this->getImage('visible-icon.png',array('style' => 'admin_grid_header_icon'));
        $col[] = $this->getImage('admin_trash_icon.png',array('style' => 'admin_grid_header_icon'));
        $this->data->header[] = $this->getRow($col,array('padding' => '10 10 10 10','background-color' => $this->lightcolor));
        unset($col);

        foreach($results as $result){

            if ( !isset($result['playid']) OR !is_numeric($result['playid']) ) {
                continue;
            }

            $playid = $result['playid'];
            $vars = AeplayVariable::getArrayOfPlayvariables($playid);

            if ( $hide_users_without_names AND (!isset($vars['name']) OR empty($vars['name'])) ) {
                continue;
            }

            $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous.png';
            $name = isset($vars['name']) ? $vars['name'] : 'unknown';
            $flag = $result['flag'];

            if($hidephotos){
                $col[] = $this->getImage('hide-icon.jpg',array('style' => 'admin_grid_header_icon'));
            } else {
                $col[] = $this->getImage($profilepic,array('imgwidth' => '150','imgheight' => '150','priority' => '9', 'style' => 'admin_grid_photocell','onclick' => $this->openuserLink($playid)));
            }

            $col[] = $this->getText($name,array('style' => 'admin_grid_cell','onclick' => $this->openuserLink($playid)));
            $col[] = $this->getText($flag,array('style' => 'admin_grid_photocell'));

            if(isset($vars['hide_user']) AND $vars['hide_user'] == 1){
                $onclick = new StdClass();
                $onclick->id = 'unhide-'.$playid;
                $onclick->action = 'submit-form-content';
                $col[] = $this->getImage('hide-icon.jpg',array('style' => 'admin_trash_icon','onclick' => $onclick));
            } else {
                $onclick = new StdClass();
                $onclick->id = 'hide-'.$playid;
                $onclick->action = 'submit-form-content';
                $col[] = $this->getImage('visible-icon.png',array('style' => 'admin_trash_icon','onclick' => $onclick));
            }

            $col[] = $this->getImage('admin_trash_icon.png',array('style' => 'admin_trash_icon','onclick' => $this->deluserLink($playid)));
            $this->data->scroll[] = $this->getRow($col,array('margin' => '10 10 0 10'));
            unset($col);

        }

        //$this->data->footer[] = $this->getText('{#you_can_hide_and_delete#}',array('style' => 'general_admin_text'));
    }

    public function deluserLink($playid){
        $deluser = new StdClass();
        $deluser->id = 'deluser-'.$playid;
        $deluser->action = 'submit-form-content';
        return $deluser;

    }

    public function openuserLink($playid){
        $openuser = new StdClass();
        $openuser->id = 'openuser-'.$playid;
        $openuser->action = 'submit-form-content';
        return $openuser;
    }

    public function userListingVariables($results){

        $col[] = $this->getText('',array('style' => 'admin_grid_header_photo'));
        $col[] = $this->getText('{#name#}',array('style' => 'admin_grid_header'));
        $col[] = $this->getText('{#match#}',array('style' => 'admin_grid_header'));
        $col[] = $this->getText('{#variable#}',array('style' => 'admin_grid_header'));
        $this->data->header[] = $this->getRow($col,array('padding' => '10 10 10 10','background-color' => $this->lightcolor));
        unset($col);

        foreach($results as $result){

            $playid = $result['play_id'];
            $searchresult = $result['value'];
            $vars = AeplayVariable::getArrayOfPlayvariables($playid);
            $varname = $result['name'];

            $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous.png';
            $name = isset($vars['name']) ? $vars['name'] : 'unknown';

            $col[] = $this->getImage($profilepic,array('style' => 'admin_grid_photocell','onclick' => $this->openuserLink($playid)));
            $col[] = $this->getText($name,array('style' => 'admin_grid_cell','onclick' => $this->openuserLink($playid)));
            $col[] = $this->getText($searchresult,array('style' => 'admin_grid_cell'));
            $col[] = $this->getText($varname,array('style' => 'admin_grid_cell'));

            $this->data->scroll[] = $this->getRow($col,array('margin' => '10 10 0 10'));
            unset($col);

        }
    }

    public function searchFilters(){

        $one[] = $this->getTextbutton('{#all#}',array('id' => 'filter-all','style' => 'admin_searchfilter_selected'));
        $col[] = $this->getColumn($one,array('background-color' => '#ffffff','width' => '90','border-radius' => '8','margin' => '7 7 7 7'));

        $two[] = $this->getTextbutton('{#email#}',array('id' => 'filter-email','style' => 'admin_searchfilter'));
        $col[] = $this->getColumn($two,array('background-color' => $this->color_topbar_hilite,'width' => '90','border-radius' => '8','margin' => '7 7 7 7'));

        $three[] = $this->getTextbutton('{#chat#}',array('id' => 'filter-email','style' => 'admin_searchfilter'));
        $col[] = $this->getColumn($three,array('background-color' => $this->color_topbar_hilite,'width' => '90','border-radius' => '8','margin' => '7 7 7 7'));

        $this->data->header[] = $this->getRow($col,array('background-color' => $this->color_topbar,'padding' => '0 0 6 0'));

    }


    public function setStatsView(){
        $count = 1;

        $statsmodel = new AppStatistics();
        $statsmodel->gid = $this->gid;

        $number_of_users = $statsmodel->statsNumberOfUsers();
        $finished_registrations = $statsmodel->finishedRegistrations();
        $chat_messages = $statsmodel->chatMessages();
        $push_messages = $statsmodel->pushMessages();
        $sessions = $statsmodel->statsSessions(true);
        $newusers = $statsmodel->statsNewplays(true);


        $width = $this->screen_width/2 - 1;

        $this->data->scroll[] = $this->getText('{#all_time#}',array('font-size' => '36','text-align' => 'center','margin' => '10 0 10 0'));

        $col[] = $this->getStatisticsbox($number_of_users,array('type' => 'headerNumber','title' => '{#total_users#}','width' => $width));
        $col[] = $this->getVerticalSpacer('2');
        $col[] = $this->getStatisticsbox($finished_registrations,array('type' => 'headerNumber','title' => '{#finished_registrations#}','width' => $width));

        $this->data->scroll[] = $this->getRow($col);
        $this->data->scroll[] = $this->getSpacer('2');
        unset($col);

        $this->data->scroll[] = $this->getText('{#last#} 7 {#days#}',array('font-size' => '36','text-align' => 'center','margin' => '10 0 10 0'));

        $col[] = $this->getStatisticsbox($sessions,array('type' => 'headerNumber','title' => '{#sessions#}','width' => $width));
        $col[] = $this->getVerticalSpacer('2');
        $col[] = $this->getStatisticsbox($newusers,array('type' => 'headerNumber','title' => '{#new_users#}','width' => $width));

        $this->data->scroll[] = $this->getRow($col);
        $this->data->scroll[] = $this->getSpacer('2');
        unset($col);


        $col[] = $this->getStatisticsbox($chat_messages,array('type' => 'headerNumber','title' => '{#chat_messages#}','width' => $width));
        $col[] = $this->getVerticalSpacer('2');
        $col[] = $this->getStatisticsbox($push_messages,array('type' => 'headerNumber','title' => '{#push_messages#}','width' => $width));

        $this->data->scroll[] = $this->getRow($col);
        $this->data->scroll[] = $this->getSpacer('2');
        unset($col);



        return true;


        while($count < 7){
            if($count == 1){
                $title = '{#yesterday#}';
            } else {
                $date = strtotime('-'.$count .' days');
                $title = '{#'.date('l',$date) .'#}, ' .date('d.m',$date);
            }

            $col[] = $this->getText($title,array(
                'background-color' => $this->color_topbar_hilite,'color' => $this->colors['top_bar_text_color'],'text-align' => 'center',
                'padding' => '4 4 8 4','font-size' => '14'));

            $col[] = $this->getStatisticsbox($this->mobilematchingobj->getStats('sessions',$count),array('type' => 'headerNumber','title' => '{#sessions#}'));
            $col[] = $this->getSpacer('10');
            $col[] = $this->getStatisticsbox($this->mobilematchingobj->getStats('active_users',$count),array('type' => 'rowNumber','title' => '{#active_users#}'));
            $col[] = $this->getStatisticsbox($this->mobilematchingobj->getStats('session_length',$count) .'s',array('type' => 'rowNumber','title' => '{#average_session_length#}'));
            $col[] = $this->getStatisticsbox($this->mobilematchingobj->getStats('new_users',$count),array('type' => 'rowNumber','title' => '{#new_users#}'));
            $col[] = $this->getSwipeNavi(6,$count,array('navicolor' => 'black'));

            $swipe[] = $this->getColumn($col);
            unset($col);
            $count++;
        }

        $this->data->footer[] = $this->getText('{#all_time_statistics#}',array(
            'background-color' => $this->color_topbar_hilite,'color' => $this->colors['top_bar_text_color'],'text-align' => 'center',
            'padding' => '4 4 8 4','font-size' => '14','margin' => '10 0 0 0'));
        $this->data->footer[] = $this->getStatisticsbox($this->mobilematchingobj->getCount('matches'),array('type' => 'rowNumber','title' => '{#number_of_matches#}','invert_colors' => true));
        $this->data->footer[] = $this->getStatisticsbox($this->mobilematchingobj->getCount('messages'),array('type' => 'rowNumber','title' => '{#number_of_message#}','invert_colors' => true));

        if(isset($swipe)){
            $this->data->scroll[] = $this->getSwipearea($swipe,array('animate' => 'nudge'));
        }

    }






}