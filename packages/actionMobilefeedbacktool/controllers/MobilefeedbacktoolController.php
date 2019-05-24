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

class MobilefeedbacktoolController extends ArticleController {

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
    public $recycleable_object_names = array('dataobj','appkeyvaluestorage','userlist','msgcount','msglist');

    /* inbox includes the whole layout code, we call it on tab 1 to get the count */
    public $inbox;
    public $gifinfo;

    /* array of id's or names, that will be applied when listing people */
    public $person_filtering = array();
    public $fundamentals;
    public $msglist;
    public $dateformat = 'F j, Y g:i a';

    public function tab1(){
        $this->data = new stdClass();
        $this->points = Aeplay::getUserPlayPoints($this->playid);
        $this->generalInit();
        return $this->viewMain();
    }

    public function generalInit(){

        /* means its already initialised */
        if(isset($this->dataobj->gid) AND $this->dataobj->gid){
            return true;
        }

        $this->dataobj = new MobilefeedbacktoolModel();
        $this->dataobj->factoryInit($this);

        $this->dataobj->gid = $this->gid;
        $this->dataobj->author_id = $this->playid;
        $this->dataobj->varcontent = $this->varcontent;
        $this->dataobj->playid = $this->playid;

        $this->initKeyValueStorage();

        if(empty($this->userlist)){
            $this->userlist = $this->dataobj->initUserlist();
        }

        if(empty($this->msglist)){
            $this->msglist = $this->dataobj->getMsgListing();
        }

        $this->msgcount = $this->msglist['unread_count'];

        if(!$this->getSavedVariable('department_id')){
            $this->addDataToUserVariables();
        }

    }

    /* @var $obj MobilefeedbacktoolModel */

    public function getPeopleFeedback($obj=false){
        $output = array('to' => '','about' => '','requester' => '','subject' => '','date' => '','from' => '');

        if(!isset($this->userlist['names_by_id'])){
            return $output;
        }

        $names = $this->userlist['names_by_id'];

        /* whether we already have an object or not */
        if(isset($obj->author_id)){
            $output['from'] = isset($names[$obj->author_id]['name']) ? $names[$obj->author_id]['name'] : '{#unknown#}';
            $output['to'] = isset($names[$obj->recipient_id]['name']) ? $names[$obj->recipient_id]['name'] : $obj->pending_username;
            $output['subject'] = $obj->subject;
            $date = strtotime($obj->date);

            if($date > time()){
                $date = $date-7200;
            }

            $date = $date+7200;

            $output['date'] = date($this->dateformat,$date);
        } else {
            $output['from'] = $this->getSavedVariable('real_name');

            $selected_person = $this->sessionGet('selected_person');
            $feedback_recipient = $this->sessionGet('feedback_recipient');

            if(is_numeric($selected_person) AND isset($names[$selected_person]['name'])){
                $output['to'] = $names[$selected_person]['name'];
            } else {
                $output['to'] = $this->sessionGet('selected_person');
            }

            if(is_numeric($feedback_recipient) AND isset($names[$feedback_recipient]['name'])){
                $output['about'] = $names[$feedback_recipient]['name'];
            } else {
                $output['about'] = $this->sessionGet('feedback_recipient');
            }

            $output['subject'] = $this->getSubmittedVariableByName('temp_subject');
            $output['date'] = date($this->dateformat,time());

        }

        return $output;
    }
    
    public function getFundamentalNameById($id){

        if(!$id){
            return false;
        }

        if(isset($this->userlist['fundamentals'])){
            $fundamentals = $this->userlist['fundamentals'];
        }

        if(isset($fundamentals[$id])){
            return $fundamentals[$id];
        }

        $obj = MobilefeedbacktoolfundamentalsModel::model()->findByPk($id);

        if(isset($obj->title)){
            return $obj->title;
        }

        return '{#none#}';
    }

    /* @var $obj MobilefeedbacktoolModel */

    public function getPeopleRequest($obj=false){

        $output = array('to' => '','about' => '','requester' => '','subject' => '','date' => '','from' => '','fundamental' => '');

        if(!isset($this->userlist['names_by_id'])){
            return $output;
        }

        $names = $this->userlist['names_by_id'];

        /* whether we already have an object or not */
        if(is_object($obj)){
            $output['to'] = isset($names[$obj->author_id]['name']) ? $names[$obj->author_id]['name'] : $obj->pending_author_username;
            $output['about'] = isset($names[$obj->recipient_id]['name']) ? $names[$obj->recipient_id]['name'] : $obj->pending_username;
            $output['requester'] = isset($names[$obj->requester_id]['name']) ? $names[$obj->requester_id]['name'] : '{#unknown#}';
            $output['subject'] = $obj->subject;
            $output['fundamental'] = $this->getFundamentalNameById($obj->fundamentals_id);
            $date = strtotime($obj->date);

            if($date > time()){
                $date = $date-7200;
            }
            $date = $date+7200;
            $output['date'] = date($this->dateformat,$date);
        } else {
            $output['requester'] = false;
            $output['from'] = $this->getSavedVariable('real_name');

            $selected_person = $this->sessionGet('selected_person');
            $feedback_recipient = $this->sessionGet('feedback_recipient');

            if(is_numeric($selected_person) AND isset($names[$selected_person]['name'])){
                $output['to'] = $names[$selected_person]['name'];
            } else {
                $output['to'] = $this->sessionGet('selected_person');
            }

            if(is_numeric($feedback_recipient) AND isset($names[$feedback_recipient]['name'])){
                $output['about'] = $names[$feedback_recipient]['name'];
            } else {
                $output['about'] = $this->sessionGet('feedback_recipient');
            }

            $output['subject'] = $this->getSubmittedVariableByName('temp_subject');
            $output['fundamental'] = $this->getFundamentalNameById($this->sessionGet('fundamental'));
            $output['date'] = date($this->dateformat,time());
        }

        return $output;
    }



    /* this function is rather complicated because we have so many
    different state for message and both id-based and pending name-based
    ways to identify the different parties */

    public function getMsgHeader($id=false,$mode=false,$obj=false){

        if(!is_object($obj) AND $id){
            $obj = MobilefeedbacktoolModel::model()->findByPk($id);
        }

        if($mode == 'request'){
            $fields = $this->getPeopleRequest($obj);
        } else {
            $fields = $this->getPeopleFeedback($obj);
        }

        return $this->getMsgHeaderLayout($fields,$mode);
    }


    public function getMsgHeaderLayout($fields,$mode=false){
        $style = array('width' => '80','margin' => '7 15 2 13','font-size' => 13,'color' => $this->color_topbar);

        $col[] = $this->getSpacer(6);

        foreach($fields as $key=>$field){
            if($field){
                $row[] = $this->getText('{#' .$key .'#}:',$style);
                $row[] = $this->getText((string)$field,array('font-size' => 13,'margin' => '7 0 2 0'));
                $col[] = $this->getRow($row,array('style' => 'msg'));
                unset($row);
            }
        }

        $col[] = $this->getSpacer(10);
        $rr[] = $this->getColumn($col);

        if($mode){
            $rr[] = $this->getText(strtoupper($mode),array('floating' => 1,'float' => 'right','margin' => '10 10 0 0',
                'background-color' => $this->color_topbar,'color' => '#ffffff','border-radius' => '35',
                'width' => '70', 'height' => '70','text-align' => 'center','font-size' => '11'));
        }

        return $this->getRow($rr,array('style' => 'general_shadowbox'));

    }

    public function addDataToUserVariables(){

        if(isset($this->userlist['current_user'])){
            $data = $this->userlist['current_user'];
            $this->saveVariable('title',$data['title']);
            $this->saveVariable('department_id',$data['department_id']);
            $this->saveVariable('supervisor_employee_id',$data['supervisor_employee_id']);
            $this->saveVariable('supervisor_employee_name',$data['supervisor_employee_name']);
            $this->saveVariable('email',$data['email']);
            $this->saveVariable('first_name',$data['first_name']);
            $this->saveVariable('last_name',$data['last_name']);
            $this->saveVariable('real_name',$data['name']);
            $this->dataobj->transferMessages($this->getSavedVariable('real_name'));
            $this->data->onload[] = $this->getOnclick('list-branches');
        }
    }

    public function viewMain(){
        $this->data = new stdClass();
        $this->rewriteActionField('subject',$this->getSavedVariable('real_name'));
        $this->getTop();
        $this->askPushPermission();
        $this->swiper();
        return $this->data;
    }


    public function happinessIndicator($title){

        $ratio = $this->screen_width / $this->screen_height;

        if($ratio > 0.6){
            $position = 'scroll';
        } else {
            $position = 'footer';
        }


            $this->data->$position[] = $this->formkitTitle($title);
            $params  = array(
            'variable' => $this->getVariableId('temp_happiness'),
            'min_value' => 1,
            'max_value' => 10,
            'value' => 5,
            'step' => 1,
            'left_track_color' => '#a4c97f',
            'right_track_color' => '#000000',
            'width' => '60%',
            'margin' => '0 10 0 15',
            'track_height' => '1',
            'vertical-align' => 'middle'
        );

        unset($col);
        $col[] = $this->getImage('sad-icon-filled-grey.png');
        $col[] = $this->getRangeslider('',$params);
        $col[] = $this->getImage('happy-icon-filled-grey.png');
        $this->data->$position[] = $this->getRow($col,array('text-align' => 'center','margin' => '15 0 10 0'));

    }

    public function getFeedbackToolButtons($buttons,$location='footer'){

        if(count($buttons) == 1){
            $width = '60%';
        } elseif(count($buttons) == 2){
            $width = '30%';
        } elseif(count($buttons) == 3){
            $width = '25%';
        } else {
            $width = '20%';
        }

        foreach($buttons as $button){
            if(!isset($button['title']) OR !isset($button['onclick'])){
                continue;
            }
            $col[] = $this->getText($button['title'],array('onclick' => $button['onclick'],
                'width' => $width,
                'background-color' => $this->color_topbar,'border-radius' => '8',
                'padding' => '10 4 10 4','color' => '#ffffff','text-align' => 'center','text-size' => '12'));
            $col[] = $this->getVerticalSpacer('3%');
        }

        if(isset($col)){
            $this->data->{$location}[] = $this->getRow($col,array('text-align' => 'center','margin' => '10 0 10 0'));
        }
    }

    public function getGifUrls($results,$sizelimit='2000000'){
        $count = 1;
        $urls = array();

        if(!isset($results['data'])){
            return false;
        }

        foreach($results['data'] as $gif) {

        if (isset($gif['images']['original']['width'])) {
            $width = $gif['images']['original']['width'];
            $height = $gif['images']['original']['height'];
            $url = $gif['images']['original']['url'];
            $ratio = $width / $height;
            $size = $gif['images']['original']['size'];
            $this->gifinfo[$url] = $gif['images']['original'];

            if ($ratio > 1.1 AND $ratio < 1.45 AND $size < $sizelimit) {
                $urls[] = $url;
                $count++;
                if ($count == 6) {
                    break;
                }
                }
            }
        }

        if(empty($urls) AND $sizelimit == '2000000'){
            return $this->getGifUrls($results,'3500000');
        }

        return $urls;

    }


    public function addFundamentals($title = '{#choose_an_area_your_feedback_is_related_to#}',$target_tab=4){
        $this->data->scroll[] = $this->getShadowbox($title);

        /* usually they should be found here */
        if(isset($this->userlist['fundamentals']) AND !empty($this->userlist['fundamentals'])){
            foreach($this->userlist['fundamentals'] as $key=>$value){
                $this->data->scroll[] = $this->selectionButton($value,$key,'fundamental',$target_tab);
            }

            $this->data->scroll[] = $this->selectionButton('{#not_related_to_any_area#}','0', 'fundamental',$target_tab);
            return true;
        }

        if(!$this->fundamentals AND $this->getSavedVariable('gifit_team_id')){
            $fundamentals = MobilefeedbacktoolfundamentalsModel::model()->findAllByAttributes(array('game_id' => $this->gid,'team_id' => $this->getSavedVariable('gifit_team_id')), array('order' => 'title'));
        } elseif(!$this->fundamentals){
            $fundamentals = MobilefeedbacktoolfundamentalsModel::model()->findAllByAttributes(array('game_id' => $this->gid));
        }

        if(empty($fundamentals)){
            MobilefeedbacktoolfundamentalsModel::addDefaults($this->gid);
            $fundamentals = MobilefeedbacktoolfundamentalsModel::model()->findAllByAttributes(array('game_id' => $this->gid));
        }

        foreach ($fundamentals as $fundamental){
            $id = $fundamental->id;
            $name = $fundamental->title;
            $this->data->scroll[] = $this->selectionButton($name,$id,'fundamental',$target_tab);
        }

        $this->data->scroll[] = $this->selectionButton('{#not_related_to_any_area#}','0', 'fundamental',$target_tab);

    }

    public function getShadowbox($text){
        $col[] = $this->getText($text,array('style' => 'general_shadowheader'));
        return $this->getColumn($col,array('style' => 'general_shadowbox'));
    }

    public function selectionButton($name,$id,$key='fundamental',$tab=4){

        $width = $this->screen_width - 100 - 40;

        $col[] = $this->getText($name,array('font-size' => '14','margin' => '0 10 0 0', 'width' => $width));
        $col[] = $this->getImage('beak-icon.png',array('height' => '30','floating' => 1,'float' => 'right'));

        return $this->getRow($col,array('margin' => '10 40 5 40',
            'padding' => '15 10 15 10','border-color' => $this->color_topbar,'border-radius' => '8',
            'onclick' => $this->getOnclickTabAndSave($key,$id,$tab)));
    }


    /* search box component */
    public function recipientList($header=true,$tab_to_open=3,$only_my_department=false){
        //$this->dataobj->invalidateCache();

        $filter = $this->getClickParam('filter');
        if($this->menuid == 'close-search'){
            unset($this->submitvariables['searchterm_people']);
        }

        if(isset($this->submitvariables['searchterm_people']) AND $this->submitvariables['searchterm_people'] AND isset($this->userlist['names'])){
            $userdata = $this->userlist['names'];
            $filter = false;
        } elseif($filter == 'recent' OR !$filter) {
            if(!empty($this->userlist['recent_contacts'])){
                $userdata = $this->userlist['recent_contacts'];
            } elseif(!empty($this->userlist['my_department'])) {
                $userdata = $this->userlist['my_department'];
            } elseif(isset($this->userlist['names'])) {
                $userdata = $this->userlist['names'];
            }
            $filter = 'recent';
        } elseif($filter == 'department' AND !empty($this->userlist['my_department'])){
            $userdata = $this->userlist['my_department'];
            $filter = 'department';
        } else {
            $userdata = $this->userlist['by_department'];
            $filter = 'all';
        }

        if(!isset($userdata) AND isset($this->userlist['names_by_id'])){
            $userdata = $this->userlist['names_by_id'];
        }

        if($header) {
            $value = $this->getSubmitVariable('searchterm_people') ? $this->getSubmitVariable('searchterm_people') : '';
            $value = trim($value);
            $row[] = $this->getImage('search-icon-for-field.png', array('height' => '25'));
            $row[] = $this->getFieldtext($value, array('style' => 'example_searchbox_text',
                'hint' => '{#free_text_search#}', 'submit_menu_id' => 'dosearch', 'variable' => 'searchterm_people',
                //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
                'id' => 'something',
                'suggestions_style_row' => 'example_list_row', 'suggestions_text_style' => 'example_list_text',
                //'submit_on_entry' => '1'
                //,'activation' => 'initially'
            ));

            if(isset($this->submitvariables['searchterm_people']) AND $this->submitvariables['searchterm_people']){
                //$this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));
                $cc = new stdClass();
                $cc->id = 'close-search';
                $cc->action = 'submit-form-content';
                $row[] = $this->getImage('nice-cancel-icon.png',array('width' => '25','onclick' => $cc,'margin' => '1 0 0 0','floating' => 1,'float' => 'right'));
            }

            $col[] = $this->getVerticalSpacer('10');
            $col[] = $this->getRow($row, array('style' => 'example_searchbox', 'width' => '70%'));
            $col[] = $this->getTextbutton('Search', array('style' => 'example_searchbtn', 'id' => 'dosearch'));
            $this->data->header[] = $this->getRow($col, array('background-color' => $this->color_topbar, 'height' => '60'));

            unset($col);
            $this->addFiltering($filter);
        }

        /* case text search */
        if(isset($this->submitvariables['searchterm_people']) AND $this->submitvariables['searchterm_people'] AND isset($this->userlist['names'])){
            $searchterm_people = $this->submitvariables['searchterm_people'];
            $searchterm_people = trim($searchterm_people);

            foreach($userdata as $key=>$val){

                $string = $val['name'] .$val['title'] .$val['department_name'];

                if($string and $searchterm_people){
                    if(stristr($string,$searchterm_people)){
                        if(isset($this->userlist['names'][$key]['playid']) AND $this->userlist['names'][$key]['playid'] == $this->playid){
                            /* don't show user herself */
                        } elseif($this->getConfigParam('mode') == 'ask' AND $this->sessionGet('feedback_recipient') == $this->userlist['names'][$key]['playid'] AND $this->sessionGet('feedback_recipient')) {
                            /* if mode is ask, don't show the user who is asked from */
                        } elseif($this->person_filtering AND (in_array($this->userlist['names'][$key]['playid'],$this->person_filtering) OR in_array($this->userlist['names'][$key]['name'],$this->person_filtering))){
                            /* filtering applied */
                        } else {
                            $this->data->scroll[] = $this->getPersonRowPart($val,'100%',$tab_to_open);
                        }
                    }
                }

            }
        } else {

            $count = 0;

            if(isset($userdata)){
                foreach($userdata as $department){
                    if(isset($department[0]['department_name'])){
                        $totranslate = str_replace(' ', '_',$department[0]['department_name']);
                        $string = $this->localizationComponent->smartLocalize('{#' .$totranslate .'#}');
                        $this->data->scroll[] = $this->formkitTitle($string);
                    }
                    $this->userlisting($department,$tab_to_open);
                    $count++;
                }
            }

        }
    }

    public function addFiltering($filter){

        if(!$filter){
            return false;
        }

        $onclick_all = $this->getOnclick('submit',false,array('params' => array('filter' => 'all'),'id' => 'search'));
        $onclick_recent = $this->getOnclick('submit',false,array('params' => array('filter' => 'recent'),'id' => 'search'));
        $onclick_mydepartment = $this->getOnclick('submit',false,array('params' => array('filter' => 'department'),'id' => 'search'));

        if($filter == 'recent'){
            $col[] = $this->getText('{#recent#}',array('style' => 'search_filter_tag_selected'));
        } else {
            $col[] = $this->getText('{#recent#}',array('style' => 'search_filter_tag','onclick' => $onclick_recent));
        }

        if($filter == 'department') {
            $col[] = $this->getText('{#my_department#}',array('style' => 'search_filter_tag_selected'));
        } else {
            $col[] = $this->getText('{#my_department#}',array('style' => 'search_filter_tag','onclick' => $onclick_mydepartment));
        }

        if($filter == 'all'){
            $col[] = $this->getText('{#show_all#}',array('style' => 'search_filter_tag_selected'));
        } else {
            $col[] = $this->getText('{#show_all#}',array('style' => 'search_filter_tag','onclick' => $onclick_all));
        }

        $col[] = $this->getText('');

        $this->data->header[] = $this->getRow($col,array('background-color' => $this->color_topbar,'padding' => '7 7 12 18','height' => '50'));

    }

    public function userlisting($data,$tab_to_open=3,$keyname='selected_person'){
        $count = 0;


        //echo($this->sessionGet('feedback_recipient'));die();

        foreach($data as $key=>$val){
            $count++;

            if($count == 20){
                break;
            }

            if(isset($val['playid']) AND $val['playid'] != $this->playid){

                if(in_array($val['name'],$this->person_filtering) OR in_array($val['playid'],$this->person_filtering)){
                    continue;
                }

                $this->data->scroll[] = $this->getPersonRowPart($val, '100%',$tab_to_open,$keyname);
                $this->data->scroll[] = $this->getSpacer(1,array('background-color' => '#DADADA'));

            }
        }

    }


    public function getPersonRowPart($data,$width='55%',$tab_to_open=3,$keyname='selected_person'){

        if($data['playid']){
            $id = $data['playid'];
        } else {
            $id = $data['name'];
        }

        $nextstage = $this->getOnclickTabAndSave($keyname,$id,$tab_to_open);

        if(isset($data['premium']) AND $data['premium']){
            $bgcolor = '#47c640';
            $color = '#ffffff';
        } else {
            $bgcolor = '#ffffff';
            $color = '#424242';
        }

        if($data['profilepic'] == 'anonymous.png' OR !$data['profilepic']){
            $data['profilepic'] = 'profile-image-placeholder.png';
        }

        $totranslate = str_replace(' ', '_',$data['department_name']);
        $department_string = $this->localizationComponent->smartLocalize('{#' .$totranslate .'#}');

        $row[] = $this->getImage($data['profilepic'],array('height' => '60','width' => '60','crop' => 'round','margin' => '0 5 0 0','imgwidth' => '120', 'imgheight' => '120'));
        $col[] = $this->getText($data['name'],array('padding' => '3 5 0 5','color' => $color,'font-size' => '15'));
        $col[] = $this->getText($data['title'],array('padding' => '0 5 0 5','color' => $color,'font-size' => '12'));
        $col[] = $this->getText($department_string,array('padding' => '0 5 0 5','color' => $color,'font-size' => '12'));

        $row[] = $this->getColumn($col,array('width' => '55%','vertical-align' => 'middle'));
        return $this->getRow($row,array('width' => $width,'onclick'=>$nextstage,'background-color' => $bgcolor,'padding' => '10 15 10 15','vertical-align' => 'middle'));
    }

    public function swiper(){
        $cachename = $this->playid .$this->userid.'-swiper';
        $cache = Appcaching::getGlobalCache($cachename);

        /* debugging */
        $cache = false;

        if($cache AND is_array($cache) AND isset($cache['layout'])){
            $this->data->scroll[] = $cache['layout'];
            $this->gifinfo = $cache['gifinfo'];
            return true;
        }

        $gifs = ThirdpartyServices::giphySearch('celebrate');
        $urls = $this->getGifUrls($gifs);
        $count = 1;

        if(isset($urls) and !empty($urls) AND is_array($urls)){
            $totalcount = count($urls);

            foreach ($urls as $url){
                $swiper[] = $this->getGif($url,$count,$totalcount);
                $count++;
            }

            if(isset($swiper)){
                $sw['layout'] = $this->getSwipearea($swiper);
                $sw['gifinfo'] = $this->gifinfo;
                $this->data->scroll[] = $this->getSwipearea($swiper);
                Appcaching::setGlobalCache($cachename,$sw,1200);
            }
        }
    }
    
    public function getGifImageFile($url,$imagewidth=false,$height=false){

        if(!stristr($url,'http')){
            return $this->getImage($url,array('width' => $imagewidth,'height' => $height));
        }

        $filename = Controller::copyDirectlyToImagesFolder($this->gid,md5($url).'.gif',$url);
        $imagesfolder = Controller::getDomain($this->gid) .'/documents/games/' .$this->gid .'/images/';

        if($imagewidth){
            return $this->getImage($imagesfolder.$filename,array('use_filename' => 1,'width' => $imagewidth,'height' => $height));
        } else {
            return $this->getImage($imagesfolder.$filename,array('use_filename' => 1,'width' => '100%'));
        }

    }

    public function getGif($url,$count=false,$totalcount=false,$onpage=false){

        // we send the url to the gif itself as a menuid
        $id = urlencode('send_gif_'.$url);
        $btn_this = new stdClass();
        $btn_this->action = 'open-action';
        $btn_this->id = $id;
        $btn_this->sync_open = 1;
        $btn_this->action_config = $this->getActionidByPermaname('sender');
        $btn_this->back_button = 1;

        $filename = Controller::copyDirectlyToImagesFolder($this->gid,md5($url).'.gif',$url);
        $imagesfolder = Controller::getDomain($this->gid) .'/documents/games/' .$this->gid .'/images/';

        //$imgs[] = $this->getText($imagesfolder.$filename);

        if($onpage){
            $imagewidth = $this->screen_width;
        } else {
            $imagewidth = $this->screen_width-24;
        }

        if(isset($this->gifinfo[$url])){
            $ratio = $this->gifinfo[$url]['width'] / $this->gifinfo[$url]['height'];
            $height = $imagewidth/$ratio;
        } else {
            $height = $this->screen_width / 1.3;
        }

        $ratio = $this->screen_width / $this->screen_height;
        //$page[] = $this->getText($ratio);

        if($ratio > 0.6){
            $height = $height-30;
        }

        //$page[] = $this->getText($height);
        $imgs[] = $this->getGifImageFile($url,$imagewidth,$height);
        $page[] = $this->getColumn($imgs,array());

        if($count == false){
            return $this->getColumn($page,array('onclick' => $btn_this));
        }

        $btn_width = $this->screen_width/2 - 10 - 12;

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getActionidByPermaname('sender');
        $onclick->back_button = 1;
        $onclick->id = 'gif_selected_'.$url;
        $onclick->sync_open = 1;

        if($this->screen_width < 330){
            $fontsize = 11;
        } else {
            $fontsize = 13;
        }

        //$page[] = $this->getText($this->screen_width,array('margin' => '0 30 0 10'));

        $col[] = $this->getText('{#send_this_card#}',array(
            'background-color' => '#ffffff','padding' => '10 10 10 10','border-radius' => '4','text-align' => 'center',
            'border-color' => '#e2e2e2',
            //'onclick' => $this->getOnclick('id',true,'custom_card'),
            'onclick' => $onclick,
            'font-size' => $fontsize,'width' => $btn_width));

        $params['async_save'] = 1;
        $params['id'] = 'gif_url';
        $params['params']['gif_url'] = $filename;
        $onclicks[] = $this->getOnclick('submit',true);
        $onclicks[] = $this->getOnclick('open-action',true);


        $col[] = $this->getVerticalSpacer('10');
        $col[] = $this->getText('{#send_custom_feedback#}',array('background-color' => '#5edd84','padding' => '5 5 5 5','border-radius' => '4','text-align' => 'center',
            'font-size' => $fontsize,'width' =>$btn_width,'onclick' => $btn_this,'color' => '#ffffff'));

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


    public function getTop(){
        $pic = $this->getSavedVariable('profilepic');
        $ratio = $this->screen_width / $this->screen_height;

        if($ratio > 0.6){
            $row[] = $this->getColLeft();
            $row[] = $this->getVerticalSpacer('10');
            /* column 2 */
            $col1[] = $this->getImage($pic,array('width' => '40','crop' => 'round','border-width' => '1','border-color' => '#e8e8e8',
                'imgwidth' => '120','imgheight' => '120',
                'onclick' => $this->getOnclick('action',true,$this->getActionidByPermaname('profile')),
                'border-radius' => '20','margin' => '0 0 0 0'));
            $row[] = $this->getColumn($col1,array('text-align' => 'center','width' => '33%'));
            $row[] = $this->getVerticalSpacer('10');
            $row[] = $this->getColRight();

            $this->data->scroll[] = $this->getRow($row,array('text-align' => 'center','padding' => '4 0 0 0',
                'vertical-align' => 'middle',
                'height' => '55',
                'background-color' => '#ffffff','margin' => '0 0 3 0'
            ));

        } else {
            $row[] = $this->getColLeft();
            $row[] = $this->getVerticalSpacer('10');
            /* column 2 */
            $col1[] = $this->getImage($pic,array('width' => '100','crop' => 'round','border-width' => '5','border-color' => '#e8e8e8',
                'onclick' => $this->getOnclick('action',true,$this->getActionidByPermaname('profile')),
                'border-radius' => '50','margin' => '0 0 10 0'));
            $row[] = $this->getColumn($col1,array('text-align' => 'center','width' => '33%'));
            $row[] = $this->getVerticalSpacer('10');
            $row[] = $this->getColRight();

            $this->data->scroll[] = $this->getRow($row,array('text-align' => 'center','padding' => '10 0 0 0',
                'shadow-color' => '#33000000','shadow-radius' => '3','shadow-offset' => '0 0',
                'vertical-align' => 'middle',
                'height' => '130',
                'background-color' => '#ffffff','margin' => '0 0 10 0'
            ));
        }

    }

    public function sendEmail($to,$subject=false,$message=false){

        if(is_numeric($to) AND isset($this->userlist['names_by_id'][$to]['email'])){
            $to = $this->userlist['names_by_id'][$to]['email'];
        } elseif(isset($this->userlist['names'][$to]['email'])){
            $to = $this->userlist['names'][$to]['email'];
        } else {
            return false;
        }

        $appname = isset($this->appinfo->name) ? $this->appinfo->name : 'Appzio';
        if(!$subject) $subject = '{#join#} '.$appname;
        if(!$message) $message = $this->getSavedVariable('real_name') .' {#sent_you_a_message#}';

        $body = $this->localizationComponent->smartLocalize('{#register_for#} '.$appname);
        $body .= "<br /><br />";
        $body .= $this->localizationComponent->smartLocalize($message .'. {#install#} ' .$appname .' {#to_read_it#}.');
        $body .= "<br /><br />";

        if(isset($this->mobilesettings->appstore_url) AND isset($this->mobilesettings->playstore_url) AND $this->mobilesettings->appstore_url AND
            $this->mobilesettings->playstore_url){
            $body .= $this->localizationComponent->smartLocalize('{#download_for_ios#}');
            $body .= "<br />";
            $body .= $this->mobilesettings->appstore_url;
            $body .= "<br />";
            $body .= "<br />";

            $body .= $this->localizationComponent->smartLocalize('{#download_for_android#}');
            $body .= "<br />";
            $body .= $this->mobilesettings->playstore_url;
            $body .= "<br />";
            $body .= "<br />";
        }

        $body .= $this->localizationComponent->smartLocalize('{#best#},');
        $body .= "<br />";
        $body .= $this->localizationComponent->smartLocalize('{#email_signature_message#}');

        Aenotification::addUserEmail( $this->playid, $subject, $body, $this->gid, $to );
    }

    /* recipient can be either an id or name */
    public function feedbackNotify($recipient,$case,$message=false,$subject=false,$menuid=false){

        if(!$recipient){
            return false;
        }

        if(!$message AND !$subject){
            switch($case){
                case 'feedback-request':
                    $subject = '{#request_to_provide_feedback#}';
                    $message = $this->getSavedVariable('real_name') .' {#requested_you_to_submit_feedback#}';
                    break;

                case 'feedback-response':
                    $subject = '{#a_new_comment_to_feedback_you_provided#}';
                    $message = $this->getSavedVariable('real_name') .' {#submitted_a_comment_to_your_feedback#}';
                    break;

                /* aka new feedback */
                default:
                    $subject = '{#new_feedback#}';
                    $message = $this->getSavedVariable('real_name') .' {#sent_you_feedback#}';
                    break;
            }
        }

        $actionid = $this->getActionidByPermaname('mailbox');

        if(is_numeric($recipient)){

            $notifications = new Aenotification();
            $notifications->id_channel = 1;
            $notifications->app_id = $this->gid;
            $notifications->play_id = $recipient;
            $notifications->subject = $subject;
            $notifications->message = $message;
            $notifications->type = 'push';
            $notifications->action_id = $actionid;

            if($menuid){
                $menu1 = new stdClass();
                $menu1->action = 'open-action';
                $menu1->id = $menuid;
                $menu1->action_config = $actionid;
                $menu1->sync_open = 1;
                $params = new stdClass;
                $params->onopen = array( $menu1 );
                $notifications->parameters = json_encode($params);
            }

            $notifications->badge_count = +1;
            $notifications->insert();
        } else {
            $this->sendEmail($recipient,$subject,$message);
        }

    }




    public function filterUserData($data,$playids=array(),$names=array(),$departmentids=array()){

        foreach($data as $key=>$item){
            if($item['playid'] == $this->playid){
                unset($data[$key]);
            }
            if(in_array($item['playid'],$playids)){
                unset($data[$key]);
            }

            if(in_array($item['name'],$names)){
                unset($data[$key]);
            }

            if(in_array($item['department_id'],$departmentids)){
                unset($data[$key]);
            }
        }

        $data = array_values($data);
        return $data;
    }











}