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

class MobilefeedbacktoolModel extends ArticleModel {

    public $game_id;
    public $author_id;
    public $recipient_id;
    public $pic;
    public $subject;
    public $message;
    public $rating;
    public $comment;
    public $feedback_rating;
    public $date;
    public $recipient_email;
    public $gid;

    public $pending_username;
    public $comment_read;
    public $msg_read;

    public $userlist;
    public $userinfo;
    public $fundamentals_id;
    public $excellent;
    public $to_maintain;
    public $to_change;
    public $departments_by_id;
    public $departments_by_name;
    public $requester_id;
    public $pending_author_username;
    public $is_request;
    public $request_subject;
    public $request_message;
    public $departments;
    public $playid;
    public $department_id_recipient;
    public $department_id_sender;

    public $msg_archived;
    public $comment_archived;

    public $request_comment_read;
    
    /* @var MobilefeedbacktoolController */
    public $factory;

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobilefeedbacktool';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'aegame' => array(self::BELONGS_TO, 'aegame', 'game_id'),
        );
    }

    public function findUser($data,$debug=false){


        if(!isset($data['department_name']) AND !isset($data['department'])){
            return false;
        }

        $department = isset($data['department_name']) ? $data['department_name'] : $data['department'];
        $name = isset($data['first_name']) ? $data['first_name'] .' ' .$data['last_name'] : false;

        if(!$name AND isset($data['real_name'])){
            $name = $data['real_name'];
        }


        $sql = "SELECT tbl1.play_id,tbl1.value,tbl2.value,tbl3.value FROM ae_game_play_variable AS tbl1
                LEFT JOIN ae_game_play_variable AS tbl2 ON tbl1.play_id = tbl2.play_id
                LEFT JOIN ae_game_play_variable AS tbl3 ON tbl1.play_id = tbl3.play_id

                LEFT JOIN ae_game_variable AS vartable1 ON tbl1.variable_id = vartable1.id
                #LEFT JOIN ae_game_variable AS vartable2 ON tbl2.variable_id = vartable2.id
                LEFT JOIN ae_game_variable AS vartable3 ON tbl3.variable_id = vartable3.id
                
                WHERE tbl1.`value` = :name
                AND vartable1.`name` = 'real_name'
                AND tbl2.`value` = :department
                AND tbl3.`value` = 'complete'
                AND vartable1.game_id = :gid
                #AND vartable2.game_id = :gid
                AND vartable3.game_id = :gid

                ORDER BY tbl1.play_id DESC
                ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':name' => $name,
                ':department' => $department,
                ':gid' => $this->gid
            ))
            ->queryAll();


        if(!isset($rows[0]['play_id'])){
            return false;
        } else{
            return $rows[0]['play_id'];
        }


    }



    public function getMsgListing(){

        $real_name = isset($this->factory->vars['real_name']) ? $this->factory->vars['real_name'] : false;
        $profilepic = isset($this->factory->vars['profilepic']) ? $this->factory->vars['profilepic'] : false;

        $sql = "SELECT ae_ext_mobilefeedbacktool.*,
            ae_ext_mobilefeedbacktool.id AS feedbackid,

            author.value AS author_name,author.variable_id,
            recipient.value AS recipient_name,recipient.variable_id,
            requester.value AS requester_name,requester.variable_id,

            authorpic.value AS author_pic,authorpic.variable_id,
            recipientpic.value AS recipient_pic,recipientpic.variable_id,
            requesterpic.value AS requester_pic,requesterpic.variable_id

            FROM ae_ext_mobilefeedbacktool 
            
                LEFT JOIN ae_game_play_variable AS author ON ae_ext_mobilefeedbacktool.author_id = author.play_id AND author.variable_id = :realnamevar
                LEFT JOIN ae_game_play_variable AS recipient ON ae_ext_mobilefeedbacktool.recipient_id = recipient.play_id AND recipient.variable_id = :realnamevar
                LEFT JOIN ae_game_play_variable AS requester ON ae_ext_mobilefeedbacktool.requester_id = requester.play_id AND requester.variable_id = :realnamevar

                LEFT JOIN ae_game_play_variable AS authorpic ON ae_ext_mobilefeedbacktool.author_id = authorpic.play_id AND authorpic.variable_id = :profilepicvar
                LEFT JOIN ae_game_play_variable AS recipientpic ON ae_ext_mobilefeedbacktool.recipient_id = recipientpic.play_id AND recipientpic.variable_id = :profilepicvar
                LEFT JOIN ae_game_play_variable AS requesterpic ON ae_ext_mobilefeedbacktool.requester_id = requesterpic.play_id AND requesterpic.variable_id = :profilepicvar

                    WHERE
                    (recipient_id = :playid OR author_id = :playid OR requester_id = :playid)
                    AND ae_ext_mobilefeedbacktool.game_id = :gameid
                    GROUP BY ae_ext_mobilefeedbacktool.id ORDER BY ae_ext_mobilefeedbacktool.id DESC 
                    ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':playid' => $this->playid,
                ':gameid' => $this->gid,
                ':realnamevar' => $real_name,
                ':profilepicvar' => $profilepic
            ))
            ->queryAll();


        return $this->mailboxQueryToArray($rows);


    }

    public function mailboxQueryToArray($rows){
        foreach ($rows as $row){
            if(!$row['author_name'] AND $row['pending_author_username']){
                $row['author_name'] = $row['pending_author_username'];
            }

            if(!$row['recipient_name'] AND $row['pending_username']){
                $row['recipient_name'] = $row['pending_username'];
            }

            /* remove feedback requests
                this is left, because there was some reason for it existing ...
            */

            if($row['recipient_id'] == $this->playid AND $row['is_request'] ){
            } else {
            }

            $newrows[] = $row;

        }

        if(isset($newrows)) {
            foreach ($newrows as $row) {
                $row['archived'] = 0;
                $row['incoming'] = 1;
                $row['otheruser_pic'] = false;
                $row['otheruser_name'] = false;
                $row['flag'] = false;

                /* this is an incoming feedback */
                if ($row['recipient_id'] == $this->playid AND !$row['requester_id']) {

                    $row['otheruser_pic'] = $row['author_pic'];
                    $row['otheruser_name'] = $row['author_name'];

                    if ($row['msg_read'] == 0) {
                        $row['flag'] = 1;
                        $unread_feedbacks[] = $row;
                        $unread_all[] = $row;
                    }

                    if ($row['msg_archived'] == 1) {
                        $row['archived'] = 1;
                        $archived[] = $row;
                    } else {
                        $inbox[] = $row;
                    }

                /* outgoing request */
                } elseif ($row['requester_id'] == $this->playid) {
                    if($row['recipient_id'] == $this->playid){
                        $row['otheruser_pic'] = $row['author_pic'];
                        $row['otheruser_name'] = $row['author_name'];
                    } else {
                        $row['otheruser_pic'] = $row['recipient_pic'];
                        $row['otheruser_name'] = $row['recipient_name'];
                    }

                    if ($row['msg_archived'] == 1) {
                        $row['archived'] = 1;
                        $archived[] = $row;
                    } else {
                        $row['incoming'] = 0;
                        $outbox[] = $row;
                    }

                    if ($row['comment_read'] == 0 AND $row['feedback_rating'] == 0 AND $row['rating'] > 0) {
                        $row['flag'] = 1;
                        $unread_all[] = $row;
                    }


                    /* this can be either a sent feedback or feedback requested by someone else
                    this is because we pass the same object from feedback request to actual feedback */
                } elseif ($row['author_id'] == $this->playid) {

                    /* means that it actually belongs to inbox instead of outbox */
                    if ($row['is_request']) {
                        $row['otheruser_pic'] = $row['requester_pic'];
                        $row['otheruser_name'] = $row['requester_name'];
                        $row['incoming'] = 1;

                        if ($row['comment_archived'] == 1) {
                            $row['archived'] = 1;
                            $archived[] = $row;
                        } else {
                            $inbox[] = $row;
                        }

                        if ($row['feedback_rating'] AND !$row['request_comment_read']) {
                            $row['flag'] = 1;
                            $unread_all[] = $row;
                        } elseif (!$row['rating']) {
                            $row['flag'] = 1;
                            $unread_all[] = $row;
                        }

                    } else {
                        $row['otheruser_pic'] = $row['recipient_pic'];
                        $row['otheruser_name'] = $row['recipient_name'];
                        $row['incoming'] = 0;

                        if ($row['feedback_rating'] > 0 AND $row['comment_read'] == 0) {
                            $row['flag'] = 1;
                            $unread_comments[] = $row;
                            $unread_all[] = $row;
                        }

                        if ($row['comment_archived'] == 1) {
                            $row['archived'] = 1;
                            $archived[] = $row;
                        } else {
                            $outbox[] = $row;
                        }
                    }


                }

                $all[] = $row;
            }
        }


        $output['all_messages'] = isset($all) ? $all : array();
        $output['inbox'] = isset($inbox) ? $inbox : array();
        $output['outbox'] = isset($outbox) ? $outbox : array();
        $output['unread_feedbacks'] = isset($unread_feedbacks) ? $unread_feedbacks : array();
        $output['unread_comments'] = isset($unread_comments) ? $unread_comments : array();
        $output['unread_all'] = isset($unread_all) ? $unread_all : array();
        $output['archived'] = isset($archived) ? $archived : array();
        $output['unread_count'] = count($output['unread_all']);
        $output['total_count'] = count($rows);

        return $output;
    }

    public function getUnreadCount(){

        $sql = "SELECT id FROM ae_ext_mobilefeedbacktool WHERE
                    # case for normal feedback
                    ((recipient_id = :playid AND msg_read = 0 AND is_request = 0) 
                    
                    # case for feedback request
                    OR (author_id = :playid AND is_request = 1 AND feedback_rating = 0) 
                  
                    # case for feedback comment
                    OR (author_id = :playid AND rating > 0 AND feedback_rating > 0 AND comment <> '' AND comment_read = '0'))
                    
                    AND game_id = :gameid GROUP BY id ORDER BY id DESC 
                    ";


        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':playid' => $this->playid,
                ':gameid' => $this->gid
            ))
            ->queryAll();

        return count($rows);

    }

    public static function getInbox($playid,$gid,$archived=false){

        if($archived){
            $archived = " AND msg_archived = '1'";
        } else {
            $archived = " AND msg_archived = '0'";
        }

        $sql = "SELECT * FROM ae_ext_mobilefeedbacktool WHERE
                    ((recipient_id = :playid AND is_request = 0) OR (author_id = :playid AND is_request = 1))
                    AND game_id = :gameid $archived ORDER BY id DESC
                    ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':playid' => $playid,
                ':gameid' => $gid
            ))
            ->queryAll();

        return $rows;

    }



    /* sorry, the cache update logic here is little demented.
        We track the time when cache is reset globally and the cache
    itself locally for user */


    public function initUserlist($force_update=false){

        if(!is_object($this->factory)){
            return false;
        }

        /* user specific */
        $cachename = $this->gid.$this->playid.'-userlistdata';

        /* global */
        $cachename_timing = $this->gid.'-userlistdata-time';
        $cache_time = Appcaching::getGlobalCache($cachename_timing);

        if(!$force_update){
            $cache = Appcaching::getGlobalCache($cachename);

            if($cache AND isset($cache['time']) AND $cache_time == $cache['time']){
                $this->userlist = $cache['data'];
                return $cache['data'];
            }
        }

        if($this->factory->getSavedVariable('gifit_team_id')){
            $id = $this->factory->getSavedVariable('gifit_team_id');
            $userlist = $this->userListFromDb($id);
        } else {
            $userlist = $this->factory->appkeyvaluestorage->getOne('userlist');
            if($userlist){
                $userlist = $this->setUserlistData($userlist);
            }
        }

        if(isset($userlist) AND $userlist){
            $time = time();

            $cache['data'] = $userlist;

            if($force_update OR $cache_time+400 < time()){
                $cache['time'] = $time;
                Appcaching::setGlobalCache($cachename_timing,$time);
            } else {
                $cache['time'] = $cache_time;
            }

            Appcaching::setGlobalCache($cachename,$cache);
            return $userlist;
        }

        return false;
    }



    public function userListFromDb($team_id){
        Yii::import('application.modules.aelogic.packages.actionMobileteamadmin.models.*');

        $users = MobileteamadminmembersModel::model()->findAllByAttributes(array('team_id'=>$team_id));
        $team_admin = MobileteamadminmembersModel::model()->findByAttributes(array('team_id'=>$team_id,'role_type' => 'admin'));
        $team_info = MobileteamadminModel::model()->findByPk($team_id);

        if(is_object($team_admin) AND !empty($users) AND is_object($team_info)){
            $this->departments[$team_id] = trim($team_info->title);

            foreach ($users as $user){
                $vars = $user['play_id'] ? AeplayVariable::getArrayOfPlayvariables($user['play_id']) : array();

                $datarray['employee_id'] = (int)$user->id;
                $datarray['department_id'] = (int)$team_id;
                $datarray['first_name'] = isset($vars['first_name']) ? $vars['first_name']: $user->first_name;
                $datarray['last_name'] = isset($vars['last_name']) ? $vars['last_name']: $user->last_name;
                $datarray['name'] = isset($vars['real_name']) ? $vars['real_name']: $user->first_name .' ' .$user->last_name;
                $datarray['title'] = '';
                $datarray['email'] = isset($vars['email']) ? $vars['email']: $user->email;

                $datarray['supervisor_employee_id'] = $team_admin->id;
                $datarray['supervisor_employee_name'] = $team_admin->first_name .' ' .$team_admin->last_name;
                $datarray['department_name'] = $team_info->title;

                $depid = $datarray['department_id'];
                $name = $datarray['name'];

                if($user->play_id){
                    $datarray['playid'] = $user->play_id;
                    $datarray['profilepic'] = isset($vars['profilepic']) ? $vars['profilepic'] : false;
                    $cache['names_by_id'][$user->play_id] = $datarray;
                    if($datarray['playid'] == $this->playid){
                        $cache['current_user'] = $datarray;
                    }
                } else {
                    $datarray['playid'] = false;
                    $datarray['profilepic'] = false;
                }

                $cache['names'][$name] = $datarray;
                $cache['by_department'][$depid][] = $datarray;
                $cache['recent_contacts'][$depid][] = $datarray;
                unset($datarray);

            }

            /* all lists should include also the department on the first level */
            if(isset($cache['current_user']['department_id'])){
                $my_department = $cache['current_user']['department_id'];
                if(isset($cache['by_department'][$my_department])){
                    $cache['my_department'][$my_department] = $cache['by_department'][$my_department];
                }
            }

            $cache['departments'] = $this->departments;
            $cache['subordinates'] = $this->getSubornidates($cache);
            $this->userlist = $cache;
            return $cache;

        }

        return false;


    }



    public function setUserlistData($userlist,$force_update=false){


        $this->loadDepartments();

        $userlist = explode(chr(10),$userlist);
        $this->setDepartments();
        $recent_ids = $this->getRecents();

        foreach($userlist as $row){

            $row = explode(';',$row);

/*            employee_id	department_id /division or unit	first_name	last_name	title	email	supervisors employee_id		supervisors employee_name 	department_id /division or unit name*/

            if(!isset($row[8])){
                continue;
            }

            $datarray['employee_id'] = (int)$row[0];
            $datarray['department_id'] = (int)$row[1];
            $datarray['first_name'] = trim($row[2]);
            $datarray['last_name'] = trim($row[3]);
            $datarray['name'] = trim($datarray['first_name']) .' ' .trim($datarray['last_name']);
            $datarray['title'] = trim($row[4]);
            $datarray['email'] = trim($row[5]);
            $datarray['supervisor_employee_id'] = (int)$row[6];
            $datarray['supervisor_employee_name'] = trim($row[7]);
            $datarray['department_name'] = trim($row[8]);

            $depid = $datarray['department_id'];
            $name = $datarray['name'];

            if(!isset($this->departments[$depid]) OR !$this->departments[$depid]){
                MobilefeedbacktooldepartmentsModel::initDepartments($this->gid);
                $this->loadDepartments();
            }

            /* we use this data array first for finding the user and secondly to fill the list */
            //$datarray = array('department' => $department,'name' => $name,'email' => trim($email),'title' => $title,'manager' => $manager,'department_id' => $dept_id);

            $play = $this->findUser($datarray);

            if($play){
                $datarray['playid'] = $play;
                $datarray['profilepic'] = $this->getProfilePic($play);
                $cache['names_by_id'][$play] = $datarray;
                if($datarray['playid'] == $this->playid){
                    $cache['current_user'] = $datarray;
                }
            } else {
                $datarray['playid'] = false;
                $datarray['profilepic'] = false;
            }

            $cache['names'][$name] = $datarray;
            $cache['by_department'][$depid][] = $datarray;


            if(isset($recent_ids[$play])){
                $cache['recent_contacts'][$depid][] = $datarray;
            }

            unset($datarray);
        }


        /* all lists should include also the department on the first level */
        if(isset($cache['current_user']['department_id'])){
            $my_department = $cache['current_user']['department_id'];
            if(isset($cache['by_department'][$my_department])){
                $cache['my_department'][$my_department] = $cache['by_department'][$my_department];
            }
        }

        $cache['departments'] = $this->departments;
        $cache['subordinates'] = $this->getSubornidates($cache);
        $cache['fundamentals'] = MobilefeedbacktoolfundamentalsModel::getAll($this->gid);
        $this->userlist = $cache;
        return $cache;

    }

    public function getSubornidates($data,$myid=false){

        $output = array();

        if(!isset($data['current_user'])){
            return $output;
        }

        if(!$myid){
            $myid = $data['current_user']['employee_id'];
        }

        foreach ($data['names'] as $employee){
            if($employee['supervisor_employee_id'] == $myid){
                $output[] = $employee;
            }
        }

        return $output;

    }

    public function getRecents(){

        $output = array();

        $sql = "select * from ae_ext_mobilefeedbacktool where date >= now()-interval 3 month AND (author_id = :playid OR recipient_id = :playid)";
        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':playid' => $this->playid
            ))
            ->queryAll();

        foreach ($rows as $row){
            $authorid = $row['author_id'];
            $recipient_id = $row['recipient_id'];

            if($authorid != $this->playid AND !isset($output[$authorid])){
                $output[$authorid] = 1;
            } elseif($row['recipient_id'] != $this->playid AND !isset($output[$recipient_id])){
                $output[$recipient_id] = 1;
            }
        }

        return $output;

    }

    public function getProfilePic($play){
        $pic = 'anonymous.png';

        if($play){
            $vars = AeplayVariable::getArrayOfPlayvariables($play);
            if(isset($vars['profilepic'])){
                $pic = $vars['profilepic'];
            }
        }

        return $pic;
    }

    public function getDepartmentId($department_name){
        $list = $this->departments_by_name;

        if(!isset($list[$department_name])){
            $new = new MobilefeedbacktooldepartmentsModel();
            $new->game_id = $this->gid;
            $new->title = $department_name;
            $new->insert();
            $dept_id = $new->getPrimaryKey();
            $this->setDepartments();
        } else {
            $dept_id = $list[$department_name];
        }

        return $dept_id;

    }

    public function setDepartments(){
        /* fetch departments from the database */
        $departments_temp = MobilefeedbacktooldepartmentsModel::model()->findAllByAttributes(array('game_id' => $this->gid));
        $departments_db = array();

        if(empty($departments_temp)){
            MobilefeedbacktooldepartmentsModel::initDepartments($this->gid);
            $departments_temp = MobilefeedbacktooldepartmentsModel::model()->findAllByAttributes(array('game_id' => $this->gid));
        }

        foreach ($departments_temp as $dept){
            $id = (int)$dept['id'];
            $title = trim($dept['title']);
            $departments_db[$title] = $id;
            $this->departments_by_id[$id] = $title;
            $this->departments_by_name[$title] = $id;
        }
    }

    public static function invalidateCache($gid,$playid){
        $cachename = $gid.$playid.'-userlistdata';
        Appcaching::removeGlobalCache($cachename);
    }

    private function loadDepartments()
    {
        $departments = MobilefeedbacktooldepartmentsModel::model()->findAllByAttributes(array('game_id' => $this->gid),array('order' => 'title'));

        foreach($departments as $dept){
            $this->departments[$dept->id] = trim($dept->title);
        }
    }

    public function transferMessages($real_name){

        if(strlen($real_name) < 4){
            return false;
        }

        $items = MobilefeedbacktoolModel::model()->findAllByAttributes(array('pending_username' => $real_name,'game_id' => $this->gid));

        foreach($items as $item){
            if($this->playid){
                if(!$item->recipient_id){
                    $ob = MobilefeedbacktoolModel::model()->findByPk($item->id);
                    $ob->pending_username = '';
                    $ob->recipient_id = $this->playid;
                    $ob->update();
                }
            }
        }

        $items = MobilefeedbacktoolModel::model()->findAllByAttributes(array('pending_author_username' => $real_name,'game_id' => $this->gid));

        foreach($items as $item){
            if($this->playid){
                if(!$item->author_id){
                    $ob = MobilefeedbacktoolModel::model()->findByPk($item->id);
                    $ob->pending_author_username = '';
                    $ob->author_id = $this->playid;
                    $ob->update();
                }
            }
        }

    }



}