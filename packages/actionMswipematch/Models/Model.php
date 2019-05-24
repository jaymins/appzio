<?php

namespace packages\actionMswipematch\Models;
use Bootstrap\Models\BootstrapModel;


use Bootstrap\Models\Mobilematching;
use CException;
use packages\actionMnotifications\Models\NotificationsModel;
use packages\actionMquiz\Models\QuizSetModel;
use Yii;


class Model extends BootstrapModel {

    public $id;
    public $game_id;
    public $gid;

    public $playid_thisuser;
    public $playid_otheruser;

    /** @var \MobilematchingModel */
    public $obj_thisuser;

    /** @var \MobilematchingModel */
    public $obj_otheruser;

    /** @var \MobilematchingmetaModel */
    public $obj_mobilematchingmeta;

    /** @var \AeplayKeyvaluestorage */
    public $obj_datastorage;

    /** @var \Aechat */
    public $obj_chat;

    public $chatid;

    public $firstname_thisuser;
    public $firstname_otheruser;

    /** @var NotificationsModel */
    public $notifications;

    public $score;
    public $gender;

    public $actionid;
    public $debug;

    public $playid;

    public $flag;

    public $uservars;
    public $extra_join_query;
    public $extra_selects;

    public $send_accept_push = true;

    public $education;
    public $hindu_caste;
    public $role;
    public $is_boosted;
    public $boosted_timestamp;

    public $recycleable_object_names = array('users_nearby');

    public $errors = array();
    public $users_nearby;

    public $searchTrainers;

    public $server_time_offset = '+1 hour';

    public $send_notification = true;

    public $exclude_bookmarked = false;

    // whether to include chat info on the contact listing
    public $include_chat = false;



    use MatchingPivot;
    use Matches;
    use Messaging;
    use MatchingUser;
    use MatchingQueries;
    use Listings;
    use Checkin;

    public $pivot_variables = array(
        'age',
        'relationship_status',
        'seeking',
        'instagram_username',
        'firstname',
        'real_name',
        'nickname',
        'lastname',
        'lat',
        'lon',
        'gender',
        'religion',
        'diet',
        'tobacco',
        'alcohol',
        'zodiac_sign',
        'reg_phase',
        'profilepic',
        'profilepic2',
        'profilepic3',
        'profilepic4',
        'profilepic5',
        'profilepic6',
        'email',
        'profile_location_invisible',
        'current_venue',
        'hide_like_count',
        'hide_my_profile'
    );

    /* gets a list of configuration fields, these are used by the view */
    public function getFieldList(){
        $params = $this->getAllConfigParams();
        return array();
    }

    public function tableName()
    {
        return 'ae_ext_mobilematching';
    }

    public function relations()
    {
        return array(
            'aeaction' => array(self::BELONGS_TO, 'Aeaction', 'playtask_id'),
            'aegame' => array(self::BELONGS_TO, 'Aegame', 'game_id'),
            'user' => array(self::BELONGS_TO, 'UserGroupsUseradmin', 'user_id'),
        );
    }


    public function getFieldSet($id)
    {
        return @QuizSetModel::model()->with('question')->findAllByAttributes(['quiz_id' => $id]);
    }

    public function initMatching($foreign_id=false,$debug=true){

        $this->initMobileMatching($foreign_id);

        if(!$this->playid_thisuser){
            $this->playid_thisuser = $this->playid;
        }

        if($foreign_id){
            $this->playid_otheruser = $foreign_id;
        }

        //Mobilematching::model()->findByAttributes()

        if(!is_object($this->obj_thisuser) AND $this->playid_thisuser){
            $this->obj_thisuser = $this->findByAttributes(array('play_id' => $this->playid_thisuser));
        }

        if(!is_object($this->obj_otheruser) AND $this->playid_otheruser){
            $this->obj_otheruser = \MobilematchingModel::model()->findByAttributes(array('play_id' => $this->playid_otheruser));
        }


        $this->obj_datastorage = new \AeplayKeyvaluestorage();
        $this->obj_datastorage->play_id = $this->playid_thisuser;

//        $this->obj_mobilematchingmeta = new \MobilematchingmetaModel();
//        $this->obj_mobilematchingmeta->play_id = $this->playid_thisuser;

        if(!$this->appid OR !$this->playid_thisuser OR !$this->actionid){
            $this->errors[] = 'Problem on initialization';
            return false;
        }

        return true;

    }


    /* called by async task */
    public static function updateCities($options){
        $cities = \ThirdpartyServices::getNearbyCities($options['lat'],$options['lon'],$options['gid']);
        \AeplayVariable::updateWithName($options['playid'],'nearby_cities',json_encode($cities),$options['gid']);
    }


    /* NOTE: This function would have absolutely no context or any of the variables initialised ! */
    public static function beaconExit($payload,$actionobj){
        if(!isset($actionobj->action_id)){ return false; }

        $command2 = new \StdClass;
        $command2->action = 'open-action';
        $command2->action_config = $actionobj->action_id;

        return array($command2);
    }


    public function getStatsStatus(){
        $data = \ThirdpartyServices::getFlurryData($this->appid);

        if($data){
            return true;
        } else {
            return false;
        }
    }

    public function loadStats(){
        $cache = \Appcaching::getGlobalCache($this->appid.'statsloading');

        if(!$cache){
            \ThirdpartyServices::loadFlurryData($this->appid);
        }
    }

    public function getStats($what,$count){
        return \ThirdpartyServices::getFlurryNode($this->appid,$what,$count);
    }

    public function getTotalSwipes() {
        $totals = $this->obj_datastorage->getTotalSwipesSQL();
        return $totals;
    }

    public function getLastSwipe() {
        $data = $this->obj_datastorage->getLastSwipeSQL();
        return $data;
    }

    public function getUser($id){
        $output['matching'] = \AeplayKeyvaluestorage::model()->findByAttributes(array('play_id' => $this->playid,'value' => $id,'key' => 'two-way-matches'));
        $output['liked'] = \AeplayKeyvaluestorage::model()->findByAttributes(array('play_id' => $this->playid,'value' => $id,'key' => 'matches'));
        $output['bookmark'] = \AeplayKeyvaluestorage::model()->findByAttributes(array('play_id' => $this->playid,'value' => $id,'key' => 'bookmark'));
        $output['hidden'] = \AeplayKeyvaluestorage::model()->findByAttributes(array('play_id' => $this->playid,'value' => $id,'key' => 'un-matches'));
        $likes = \AeplayKeyvaluestorage::model()->countByAttributes(array('value' => $id,'key' => 'matches'));

        $output['vars'] = \AeplayVariable::getArrayOfPlayvariables($id);
        $output['images'] = array();
        $output['play_id'] = $id;

        foreach($output['vars'] as $key=>$var){
            if(stristr($key, 'profilepic')){
                $output['images'][] = $var;
            }
        }

        if(isset($output['vars']['instagram_followed_by']) AND $output['vars']['instagram_followed_by']){
            $output['like_count'] = $likes+$output['vars']['instagram_followed_by'];
        } else {
            $output['like_count'] = $likes;
        }

        return $output;
    }

    public function addBoomark($id)
    {
        $test = \AeplayKeyvaluestorage::model()->findByAttributes(array('play_id' => $this->playid,'key' => 'bookmark', 'value' => $id));

        if(!$test){
            $obj = new \AeplayKeyvaluestorage();
            $obj->play_id = $this->playid;
            $obj->value = $id;
            $obj->key = 'bookmark';
            $obj->insert();
        }
    }

    public function removeBoomark($id)
    {
        \AeplayKeyvaluestorage::model()->deleteAllByAttributes(array('play_id' => $this->playid,'key' => 'bookmark', 'value' => $id));
    }

    public function recordInstaClick($id)
    {
        $nickname = $this->getSavedVariable('firstname');

        $this->notifications->addNotification(array(
            'subject' => $nickname .' {#opened_your_instagram_page#} ',
            'to_playid' => $id,
            'type' => 'instagram_opened',
        ));
    }




}