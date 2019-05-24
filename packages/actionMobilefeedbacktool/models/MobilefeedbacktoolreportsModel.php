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

class MobilefeedbacktoolreportsModel extends MobilefeedbacktoolModel {

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
    public $variables;

    public $fundamentals;

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

    /*
    count: feedbacks sent
    count: feedbacks received
    overall feedback
    feedback usefulness
    by each fundamentals

            $dept = isset($this->variables['department_id']) ? $this->variables['department_id'] : false;

        $sql = "SELECT *
            ,author_department.value as author_department,recipient_department.value as recipient_department
FROM ae_ext_mobilefeedbacktool

LEFT JOIN ae_game_play_variable as author_department ON ae_ext_mobilefeedbacktool.author_id = author_department.play_id
AND author_department.variable_id = :department
LEFT JOIN ae_game_play_variable as recipient_department ON ae_ext_mobilefeedbacktool.recipient_id = recipient_department.play_id
AND recipient_department.variable_id = :department
WHERE
author_id = :playid OR recipient_id = :playid";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':playid' => $playid,
                ':department' => $dept
            ))
            ->queryAll();

    */

    public function departmentStats($department_id){
        return self::statsQuery('department',$department_id);
    }

    public function subordinatesStats($list){
        return self::statsQuery('subordinates',$list);
    }

    public function userStats($playid){
        return self::statsQuery('user',$playid);
    }


    public function exportCsv(){

        $sql = "SELECT *,
                (SELECT value FROM ae_game_play_variable WHERE author_id = ae_game_play_variable.play_id AND variable_id = '7363') as author_name,
                (SELECT value FROM ae_game_play_variable WHERE recipient_id = ae_game_play_variable.play_id AND variable_id = '7363') as recipient_name,
                (SELECT value FROM ae_game_play_variable WHERE requester_id = ae_game_play_variable.play_id AND variable_id = '7363') as requester_name,
              
                (SELECT title FROM ae_ext_mobilefeedbacktool_departments WHERE department_id_sender = ae_ext_mobilefeedbacktool_departments.id) as sender_department,
                (SELECT title FROM ae_ext_mobilefeedbacktool_departments WHERE department_id_recipient = ae_ext_mobilefeedbacktool_departments.id) as recipient_department
              
                FROM ae_ext_mobilefeedbacktool 
                
                WHERE game_id = '320'
                ";
    }



    private function statsQuery($case='user',$value){

        switch($case){
            case 'user':
                $field_receiver = 'recipient_id';
                $field_sender = 'author_id';
                break;

            case 'department':
                $field_receiver = 'department_id_recipient';
                $field_sender = 'department_id_sender';
                break;

            case 'subordinates':
                $field_receiver = 'recipient_id';
                $field_sender = 'author_id';
                break;
        }

        if($case == 'subordinates'){

            foreach($value as $item){
                if(isset($item['playid']) AND $item['playid']){
                    $list[] = $item['playid'];
                }
            }

            if(!isset($list)){
                return false;
            }

            $list = '(' .implode(',', $list) .')';

            $sql = "SELECT * FROM ae_ext_mobilefeedbacktool WHERE $field_receiver IN $list AND game_id = :game_id AND rating > 0";
            $received = Yii::app()->db->createCommand($sql)->bindValues(array(':game_id' => $this->game_id))->queryAll();

            $sql = "SELECT * FROM ae_ext_mobilefeedbacktool WHERE $field_sender IN $list AND game_id = :game_id AND rating > 0";
            $sent = Yii::app()->db->createCommand($sql)->bindValues(array(':game_id' => $this->game_id))->queryAll();

            $sql = "SELECT *,AVG(rating) AS rating_average FROM ae_ext_mobilefeedbacktool WHERE $field_receiver IN $list AND game_id = :game_id AND rating > 0";
            $received_average = Yii::app()->db->createCommand($sql)->bindValues(array(':game_id' => $this->game_id))->queryAll();


            $sql = "SELECT *,AVG(feedback_rating) AS feedback_rating FROM ae_ext_mobilefeedbacktool WHERE $field_receiver IN $list AND game_id = :game_id AND feedback_rating > 0";
            $sent_average = Yii::app()->db->createCommand($sql)->bindValues(array(':game_id' => $this->game_id))->queryAll();

        } else {
            $sql = "SELECT * FROM ae_ext_mobilefeedbacktool WHERE $field_receiver = :value AND game_id = :game_id AND rating > 0";
            $received = Yii::app()->db->createCommand($sql)->bindValues(array(':value' => $value,':game_id' => $this->game_id))->queryAll();

            $sql = "SELECT * FROM ae_ext_mobilefeedbacktool WHERE $field_sender = :value AND game_id = :game_id AND rating > 0";
            $sent = Yii::app()->db->createCommand($sql)->bindValues(array(':value' => $value,':game_id' => $this->game_id))->queryAll();

            $sql = "SELECT *,AVG(rating) AS rating_average FROM ae_ext_mobilefeedbacktool WHERE $field_receiver = :value AND game_id = :game_id AND rating > 0";
            $received_average = Yii::app()->db->createCommand($sql)->bindValues(array(':value' => $value,':game_id' => $this->game_id))->queryAll();

            $sql = "SELECT *,AVG(feedback_rating) AS feedback_rating FROM ae_ext_mobilefeedbacktool WHERE $field_sender = :value AND game_id = :game_id AND feedback_rating > 0";
            $sent_average = Yii::app()->db->createCommand($sql)->bindValues(array(':value' => $value,':game_id' => $this->game_id))->queryAll();
        }
        
        if(!isset($received[0]['id']) AND !isset($sent[0]['id'])){
            return false;
        }

        $data['rating_average'] = round($received_average[0]['rating_average']/10,1);
        $data['rating_average_count'] = count($received);

        $data['feedback_usefulness'] = round($sent_average[0]['feedback_rating']/10,1);
        $data['feedback_usefulness_count'] = count($sent);

        $data['feedbacks_sent'] = count($sent);
        $data['feedbacks_received'] = count($received);

        $data['fundamentals'] = $this->addFundamentals($received);
        return $data;
    }

    public function setTeamStats(){

    }

    public function addFundamentals($data){

        $output = array();
        $averages = array();

        foreach($data as $row){
            $fundamental = $row['fundamentals_id'];
            $output[$fundamental][] = $row['rating'];
        }


        foreach($output as $key=>$item){
            $count = count($item);
            if($count > 0 AND isset($this->fundamentals[$key])){
                $averages[$key]['value'] = (array_sum($item) / $count) / 10;
                $averages[$key]['count'] = $count;
                $averages[$key]['title'] = $this->fundamentals[$key];
            } elseif($key == 0){
                $averages[$key]['value'] = (array_sum($item) / $count) / 10;
                $averages[$key]['count'] = $count;
                $averages[$key]['title'] = '{#no_fundamental#}';
            }
        }
        
        return $averages;

    }






}