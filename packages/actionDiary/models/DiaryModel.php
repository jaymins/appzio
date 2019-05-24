<?php


/* this is the modules own model file,
    which is used by both controller & other components
 */


class DiaryModel extends CActiveRecord
{

    public $msg;
    public $subject;
    public $connection;
    public $userid;
    public $datetime;
    public $comment;
    public $coach;
    public $playtask_id;
    public $name;

    /* this really should thrown an error instead of returning false ... */

    public function init(){

		$this->connection = Yii::app()->db;
		if(!isset($this->userid) AND isset($_SERVER['SERVER_NAME'])){ $this->userid = Yii::app()->user->id; }

	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return aegame the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ae_ext_diary';
	}

    public static function sayboo(){
        echo('boo');
        die();
    }

/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
		

		);
	}

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
            array('shorturl,api_key,api_secret_key', 'unique'),
			array('active, timelimit', 'numerical', 'integerOnly'=>true),
			array('user_id, category_id', 'length', 'max'=>11),
			array('name, icon, length', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, category_id, name, active, icon, length, timelimit, featured,description,maxactions,
			show_toplist,register_email,register_sms,choose_playername,choose_avatar,home_instructions,shorturl,show_logo,show_social,show_branches,
			api_key,api_secret_key,api_enabled,api_callback_url
			', 'safe'),
		);
	}
   
    public static function getAllPosts($actionid,$userid=0){
	
		$queryStr='';
		if ($userid>0) {
			$queryStr='AND (ae_ext_diary.user_id= '.$userid.' OR ae_ext_diary.user_id=ae_game.user_id)';
		}
		
        $sql = 'SELECT *,
                      ae_ext_diary.id as entryid,
                      ae_game_branch_action.name AS actiontitle,
                      ae_ext_diary.msg AS msg,
                      ae_ext_diary.date AS maindate,
                      ae_ext_diary.parent_id AS commentparentid,
					  ae_game.user_id AS gameauthor,
                      usergroups_user.id AS userid,
					  usergroups_user.email AS useremail,
					  usergroups_user.username AS username
                    FROM ae_ext_diary
                    LEFT JOIN usergroups_user ON ae_ext_diary.user_id = usergroups_user.id
					LEFT JOIN ae_game_play_action ON ae_ext_diary.playtask_id = ae_game_play_action.id
                    LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                    LEFT JOIN ae_game_branch ON ae_game_branch_action.branch_id = ae_game_branch.id
				    LEFT JOIN ae_game ON ae_game.id = ae_game_branch.game_id
                    WHERE ae_game_branch_action.id = :actionId 
					'.$queryStr.'
                    GROUP BY entryid
                    ORDER BY commentparentid DESC, ae_ext_diary.date DESC
                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_bbs.date DESC

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':actionId' => $actionid))
            ->queryAll();

        return $rows;
    }
	
	
	 public static function getAllUsersInDiaryAction($actionid){
	
		
        $sql = 'SELECT *,
                      ae_ext_diary.id as entryid,
                      ae_game_branch_action.name AS actiontitle,
                      ae_ext_diary.msg AS msg,
                      ae_ext_diary.date AS maindate,
                      ae_ext_diary.parent_id AS commentparentid,
					  ae_game.user_id AS gameauthor,
                      usergroups_user.id AS userid,
					  usergroups_user.email AS useremail,
					  usergroups_user.username AS username
                    FROM ae_ext_diary
                    LEFT JOIN usergroups_user ON ae_ext_diary.user_id = usergroups_user.id
					LEFT JOIN ae_game_play_action ON ae_ext_diary.playtask_id = ae_game_play_action.id
                    LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                    LEFT JOIN ae_game_branch ON ae_game_branch_action.branch_id = ae_game_branch.id
				    LEFT JOIN ae_game ON ae_game.id = ae_game_branch.game_id
                    WHERE ae_game_branch_action.id = :actionId 
					AND usergroups_user.id <> ae_game.user_id
                    GROUP BY userid
                    ORDER BY commentparentid DESC, ae_ext_diary.date DESC
                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_bbs.date DESC

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':actionId' => $actionid))
            ->queryAll();

        return $rows;
    }
	
	
    
	
	 public static function getAllDiaryActions($gameid){
        $sql = 'SELECT *,
                    ae_game_branch_action.name AS actiontitle
                    FROM ae_game_play_action
                    LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                    LEFT JOIN ae_game_branch ON ae_game_branch_action.branch_id = ae_game_branch.id
					LEFT JOIN ae_game_play ON ae_game_branch.game_id = ae_game_play.game_id
                    WHERE ae_game_play.game_id = :playId
					AND ae_game_branch_action.type_id=16
					GROUP BY ae_game_play_action.action_id
                    ORDER BY ae_game_play_action.id DESC
                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_bbs.date DESC

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':playId' => $gameid))
            ->queryAll();

        return $rows;
    }

    public static function getUserVariables($gameid){

        $sql = 'SELECT *,
                    ae_game_play_variable.id AS playvariableid,
                    ae_game_variable.name AS variablename,
                    ae_game_play_variable.value AS variablevalue,
                    ae_game_variable.id AS variableid
                    FROM ae_game_play
                    LEFT JOIN usergroups_user ON ae_game_play.user_id = usergroups_user.id
                    LEFT JOIN ae_game_play_variable ON ae_game_play_variable.play_id = ae_game_play.id
                    LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id
                    LEFT JOIN ae_ext_diary ON usergroups_user.id = ae_ext_diary.user_id
                    WHERE ae_game_play.game_id = :gameId
                    AND ae_ext_diary.id IS NOT NULL
                    GROUP BY playvariableid
                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_bbs.date DESC

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':gameId' => $gameid))
            ->queryAll();

        $ret = array();

        while($play = each($rows)){
            $play = $play[1];
            $variableid = $play['variableid'];
            $id = $play['user_id'];
            $ret[$id][$variableid] = $play;
        }
       
        return $ret;
    }
    
	
	
	 public static function getGameDiary($gid){
        $sql = 'SELECT *,
                      ae_ext_diary.id as entryid,
                      ae_game_branch_action.name AS actiontitle,
                      ae_game_play_action.id AS actionid

                    FROM ae_ext_diary
                    LEFT JOIN ae_game_play_action ON ae_ext_diary.playtask_id = ae_game_play_action.id
                    LEFT JOIN ae_game_play ON ae_game_play_action.play_id = ae_game_play.id
                    LEFT JOIN usergroups_user ON ae_game_play.user_id = usergroups_user.id
                    LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                    WHERE ae_game_play.game_id = :gameId
                    GROUP BY entryid
                    ORDER BY ae_ext_diary.date DESC
                    ';

      
        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':gameId' => $gid))
            ->queryAll();

        return $rows;

    }


    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'aeaction' => array(self::BELONGS_TO, 'Aeaction', 'playtask_id'),
		);
    }

    // check that user really has access permission
    public static function checkPermissions($userid){

        if($userid != Yii::app()->user->id){
            return false;
        } else {
            return true;
        }

    }


}