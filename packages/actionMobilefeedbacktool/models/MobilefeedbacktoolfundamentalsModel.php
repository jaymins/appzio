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

class MobilefeedbacktoolfundamentalsModel extends ArticleModel {

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
    public $sorting;

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobilefeedbacktool_fundamentals';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'aegame' => array(self::BELONGS_TO, 'aegame', 'game_id'),
        );
    }

    public static function getAll($gid){

        $fundamentals = MobilefeedbacktoolfundamentalsModel::model()->findAllByAttributes(array('game_id' => $gid),array('order'=>'sorting ASC'));

        foreach($fundamentals as $fundamental){
            $output[$fundamental->id] = $fundamental->title;
        }

        if(isset($output)){
            return $output;
        }

        return false;

    }

    public static function addDefaults($gid){

        $fundamentals = array('{#customer_first#}','{#cooperation_and_synergies#}','{#execution_and_discipline#}','{#risk_management#}','{#people_development#}');

        foreach ($fundamentals as $fundamental){
            $new = new MobilefeedbacktoolfundamentalsModel;
            $new->game_id = $gid;
            $new->title = $fundamental;
            $new->insert();
        }

    }

    


}