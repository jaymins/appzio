<?php

class MobileteamadminmembersModel extends ArticleModel {

    public $team_id;
    public $play_id;
    public $role_type;
    public $email;
    public $first_name;
    public $last_name;
    public $status;

    /* @var MobileteamadminController */
    public $factory;

    public function tableName()
    {
        return 'ae_ext_mobilefeedbacktool_teams_members';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'team' => array(self::BELONGS_TO, 'MobileteamadminModel', 'team_id'),
        );
    }

    public function addInvite($email,$firstname,$lastname,$team_id,$code){

        if(!$email OR !$team_id){
            return false;
        }
        $obj = MobileteamadminmembersModel::model()->findByAttributes(array('team_id' => $team_id,'email' => $email));

        if(is_object($obj)){
            return false;
        }

        /* all validation is with lowercase */
        $code = strtolower($code);

        $obj = new MobileteamadminmembersModel();
        $obj->team_id = $team_id;
        $obj->email = strtolower($email);
        $obj->first_name = trim(ucfirst($firstname));
        $obj->last_name = trim(ucfirst($lastname));
        $obj->status = 'invited';
        $obj->role_type = 'member';
        $obj->invite_code = $code;
        $obj->insert();

        return $obj->id;


    }

    public function changeUserRole($id){
        $obj = MobileteamadminmembersModel::model()->findByPk($id);

        if(isset($obj->role_type) AND $obj->role_type=='member') {
            $obj->role_type = 'admin';
            $obj->update();
        }elseif(isset($obj->role_type) AND $obj->role_type=='admin' AND $obj->play_id != $this->factory->playid){
            $obj->role_type = 'member';
            $obj->update();
        }

    }




}