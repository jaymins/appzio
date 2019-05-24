<?php

class MobileteamadminModel extends ArticleModel {


    public $id;
    public $game_id;
    public $owner_id;
    public $title;
    public $date_created;
    public $license;

    /* @var MobileteamadminController */
    public $factory;

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobilefeedbacktool_teams';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'aegame' => array(self::BELONGS_TO, 'aegame', 'game_id'),
        );
    }

    public function createNewTeam($title){
        $obj = MobileteamadminModel::model()->findByAttributes(array('title'=>$title));
        if(is_object($obj)) return false;

        $obj = new MobileteamadminModel();
        $obj->title = $title;
        $obj->owner_id = $this->factory->playid;
        $obj->game_id = $this->factory->gid;
        $obj->insert();
        $id = $obj->getPrimaryKey();

        $team = new MobileteamadminmembersModel($this->factory);
        $team->team_id = $id;
        $team->play_id = $this->factory->playid;
        $team->role_type = 'admin';
        $team->email = strtolower($this->factory->getSavedVariable('email'));

        if(!$this->factory->getSavedVariable('first_name') AND $this->factory->getSavedVariable('real_name')){
            $name = explode(' ',$this->factory->getSavedVariable('real_name'));
            $first_name = ucfirst($name[0]);
            $lastname_name = ucfirst($name[1]);
        } else {
            $first_name = ucfirst($this->factory->getSavedVariable('first_name'));
            $lastname_name = ucfirst($this->factory->getSavedVariable('last_name'));
        }

        $team->first_name = $first_name;
        $team->last_name = $lastname_name;
        $team->status = 'active';

        $team->insert();

        return $id;

    }

    public function updateTitle($id,$newtitle){

    }

    public function findInvitations(){
        $email = $this->factory->getSavedVariable('email');
        $teams = MobileteamadminmembersModel::model()->findAllByAttributes(array('email' => $email));
        return $teams;
    }


}