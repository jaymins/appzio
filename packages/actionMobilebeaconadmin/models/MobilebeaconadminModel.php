<?php

class MobilebeaconadminModel extends ArticleModel {


    public $gid;
    public $estimotetoken;
    public $configobj;
    public $region;
    public $variables;
    public $nearby_beacons;
    public $all_beacons;


    public function init(){
        $appid = $this->getConfigParam('estimote_app_id');
        $token = $this->getConfigParam('estimote_app_token');
        $this->region = $this->getConfigParam('region');
        $this->estimotetoken = $appid.':'.$token;

        if(isset($this->variables['nearby_beacons']) AND $this->variables['nearby_beacons']){
            $beacons = json_decode($this->variables['nearby_beacons'],true);
            if(isset($beacons['beacons'])){
                $this->nearby_beacons = $beacons['beacons'];
            }
        }
    }

    public function createBeaconToDatabase($identifier){

        /* make sure we don't get duplicates */
        $obj = MobilebeaconadminModel::model()->findByAttributes(array('identifier' => $identifier));
        if(is_object($obj)){
            return false;
        }

        $data = $this->getBeaconDataByIdentifier($identifier);

        if(!$data){
            return false;
        }

        if(!isset($data['settings']['advertisers']['ibeacon'][0]['uuid'])){
            return false;
        }

        $uuid = $data['settings']['advertisers']['ibeacon'][0]['uuid'];
        $major = $data['settings']['advertisers']['ibeacon'][0]['major'];
        $minor = $data['settings']['advertisers']['ibeacon'][0]['minor'];

        $obj = new MobilebeaconadminModel();
        $obj->app_id = $this->gid;
        $obj->identifier = $identifier;
        $obj->name = 'Unnamed Beacon';
        $obj->region = $uuid;
        $obj->minor = $minor;
        $obj->major = $major;
        $obj->brand = 'estimote';
        $obj->hardware_type = $data['hardware_type'];
        $obj->color = $data['color'];
        $obj->insert();

    }


    public function updateLocation($id){
        if(!isset($this->variables['lat']) OR !isset($this->variables['lon'])){
            return false;
        }

        $location = ThirdpartyServices::geoAddressTranslation($this->variables['lat'],$this->variables['lon'],$this->gid);
        $obj = MobilebeaconadminModel::model()->findByAttributes(array('identifier' => $id));

        $obj->lat = $this->variables['lat'];
        $obj->lon = $this->variables['lon'];

        if(isset($location['city'])){ $obj->city = $location['city']; }
        if(isset($location['country'])){ $obj->country = $location['country']; }
        if(isset($location['county'])){ $obj->state = $location['county']; }
        if(isset($location['zip'])){ $obj->zipcode = $location['zip']; }
        if(isset($location['street'])){ $obj->street_name = $location['street']; }

        $obj->update();
    }


    public function getBeaconDataByIdentifier($identifier){
        if(empty($this->all_beacons)){
            $this->getAllEstimotes();
        }

        if(empty($this->all_beacons)){
            return false;
        }

        foreach ($this->all_beacons AS $beacon){
            if($beacon['identifier'] == $identifier){
                return $beacon;
            }
        }
    }



    public function getBeaconName($data){
        if(isset($data['identifier'])){
            $obj = MobilebeaconadminModel::model()->findByAttributes(array('identifier' => $data['identifier']));

            if(is_object($obj)){
                return $obj->name;
            }
        }


        return '{#record_doesnt_exist#}!';
    }

    public function getAllEstimotes(){
        $devices = Appcaching::getGlobalCache($this->gid.'-stimotes');

        if(!$devices){
            $devices = ThirdpartyServices::getAllEstimoteDevices($this->estimotetoken);
            Appcaching::setGlobalCache($this->gid.'-estimotes',$devices);
        }

        $devices = json_decode($devices,true);
        $this->all_beacons = $devices;
        return $devices;
    }

    public function isOnline($data){

        $id = self::getRegionId($data);

        if(!empty($this->nearby_beacons)){
            foreach ($this->nearby_beacons as $beacon){
                $beaconid = self::getRegionId($beacon);
/*                echo('<br><br>online:');
                echo($beaconid);
                echo('<br>saved:');
                echo($id);*/
                if($beaconid == $id){
                    return true;
                }
            }
        }

        return false;

    }


    public static function getRegionId($data){
        if(isset($data['settings']['advertisers']['ibeacon'][0])){
            $uuid = $data['settings']['advertisers']['ibeacon'][0]['uuid'];
            $major = $data['settings']['advertisers']['ibeacon'][0]['major'];
            $minor = $data['settings']['advertisers']['ibeacon'][0]['minor'];
            return $uuid .':' .$major .':' .$minor;
        }

        if(isset($data['beacon_id'])){
            return $data['beacon_id'] .':' .$data['beacon_major'] .':' .$data['beacon_minor'];

        }

    }

    public function tableName()
    {
        return 'ae_ext_mobilebeacons';
    }

    public function primaryKey()
    {
        return 'id';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }





}