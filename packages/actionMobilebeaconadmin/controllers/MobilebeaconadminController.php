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
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

class MobilebeaconadminController extends ArticleController {

    public $data;
    public $theme;
    public $estimotetoken;
    public $region;
    public $onlinebeacons;

    /* @var MobilebeaconadminModel */
    public $obj_beaconmodel;

    public $output;

    public function init(){
        $this->obj_beaconmodel = new MobilebeaconadminModel();
        $this->obj_beaconmodel->gid = $this->gid;
        $this->obj_beaconmodel->configobj = $this->configobj;
        $this->obj_beaconmodel->variables = $this->varcontent;

        $appid = $this->getConfigParam('estimote_app_id');
        $token = $this->getConfigParam('estimote_app_token');
        $this->region = $this->getConfigParam('region');
        $this->estimotetoken = $appid.':'.$token;

        $this->obj_beaconmodel->init();
    }


    public function tab1(){
        $this->data = new stdClass();
        $this->output = new stdClass();
        $this->data->scroll = array();
        $this->output->online = array();
        $this->output->offline = array();

        $this->init();
        $this->setHeader();

        switch($this->menuid){
            case 'empty-beacons':
                $this->emptyBeacons();
                return $this->data;
                break;
        }

        if(strstr($this->menuid,'openbeacon_')){
            $id = str_replace('openbeacon_','',$this->menuid);
            $this->showBeacon($id);
            return $this->data;
        }

        if(strstr($this->menuid,'createbeacon_')){
            $id = str_replace('createbeacon_','',$this->menuid);
            $this->createBeacon($id);
            return $this->data;
        }

        if(strstr($this->menuid,'update_beacon_location_')){
            $id = str_replace('update_beacon_location_','',$this->menuid);
            $this->updateBeaconLocation($id);
            return $this->data;
        }

        if(strstr($this->menuid,'save_beacon_data_')){
            $id = str_replace('save_beacon_data_','',$this->menuid);
            $this->saveBeaconData($id);
            return $this->data;
        }


        /* showing a list of all beacons */
        $this->loadVariableContent();
        $this->init();
        $devices = $this->obj_beaconmodel->getAllEstimotes();

        if(!empty($devices)){
            foreach ($devices as $device){
                $this->renderBeacon($device);
            }
        }

        /* this little flipping just to show the nearby beacons first */
        $this->data->scroll = array_merge($this->data->scroll,$this->output->online);
        $this->data->scroll = array_merge($this->data->scroll,$this->output->offline);
        $this->setFooter();
        return $this->data;
    }


    public function emptyBeacons(){
        Appcaching::removeGlobalCache($this->gid .'-estimotes');
        $this->deleteVariable('nearby_beacons');
        $this->data->scroll[] = $this->getSpacer('100');
        $this->data->scroll[] = $this->getLoader('Scanning',array('color' => '#000000'));
        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->scroll[] = $this->getText('{#scanning#}',array('text-align' => 'center'));
    }

    public function updateBeaconLocation($id){
        $this->obj_beaconmodel->updateLocation($id);
        $this->init();
        $this->showBeacon($id);
    }

    public function saveBeaconData($id){

        $obj = MobilebeaconadminModel::model()->findByAttributes(array('identifier' => $id));
        
        foreach($this->submitvariables as $key=>$varcontent){
            $fieldid = str_replace('tempvar_','',$key);
            if($varcontent){
                if(isset($obj->$fieldid) AND $obj->$fieldid != $varcontent){
                    $obj->$fieldid = $varcontent;
                }
            }
        }

        $obj->update();
        $this->showBeacon($id);
    }


    public function showBeacon($id){

        $obj = MobilebeaconadminModel::model()->findByAttributes(array('identifier' => $id));

        if(!is_object($obj)){
            $btnid = 'createbeacon_'.$id;
            $this->data->scroll[] = $this->getSpacer('30');
            $this->data->scroll[] = $this->getText('{#beacon_not_in_database#}',array('text-align' => 'center'));
            $this->data->footer[] = $this->getTextbutton('{#create_database_record#}',array('id' => $btnid));
        } else {

            $fields = $obj->getAttributes();

            $this->data->scroll[] = $this->getSettingsTitle('{#editable_fields#}');
            foreach ($fields as $key=>$field){
                if($key == 'name' OR $key == 'place_id'){
                    $this->data->scroll[] = $this->getTextFieldWithTitle('tempvar_'.$key,$key,$field);
                }
            }

            $this->data->scroll[] = $this->getSettingsTitle('{#all_data#}');
            foreach ($fields as $key=>$field){
                if($key != 'name' AND $key != 'place_id'){
                    $this->data->scroll[] = $this->getDataRow($key,$field);

                }
            }


            $col[] = $this->getTextbutton('{#update_location#}',array('id' => 'update_beacon_location_'.$id,'width' => '49%'));
            $col[] = $this->getVerticalSpacer('2%');
            $col[] = $this->getTextbutton('{#save_data#}',array('id' => 'save_beacon_data_'.$id,'width' => '49%'));
            $this->data->footer[] = $this->getRow($col);
        }
    }

    public function getDataRow($title,$value){
        $col[] = $this->getText(strtoupper($title),array('style' => 'form-field-titletext'));
        $col[] = $this->getText($value,array('style' => 'form-field-textfield'));
        $col[] = $this->getText('',array('style' => 'form-field-separator'));
        return $this->getColumn($col,array('style' => 'form-field-row'));

    }


    public function createBeacon($id){
        $this->obj_beaconmodel->createBeaconToDatabase($id);
        $this->showBeacon($id);
    }



    public function tab3(){
        $this->data = new StdClass();
        $this->obj_beaconmodel = new MobilebeaconadminModel();
        $this->obj_beaconmodel->gid = $this->gid;
        $this->obj_beaconmodel->configobj = $this->configobj;
        $this->obj_beaconmodel->variables = $this->varcontent;
        $this->obj_beaconmodel->init();

        $this->setHeader();
        $appid = $this->getConfigParam('estimote_app_id');
        $token = $this->getConfigParam('estimote_app_token');
        $this->region = $this->getConfigParam('region');
        $this->estimotetoken = $appid.':'.$token;

        if($this->menuid == 'empty-beacons'){
            Appcaching::removeGlobalCache($this->gid .'-estimotes');
            $this->deleteVariable('nearby_beacons');
            $this->data->scroll[] = $this->getSpacer('100');
            $this->data->scroll[] = $this->getLoader('Scanning',array('color' => '#000000'));
            $this->data->scroll[] = $this->getSpacer('10');
            $this->data->scroll[] = $this->getText('{#scanning#}',array('text-align' => 'center'));
            return $this->data;
        }

        $this->data->scroll[] = $this->getText($this->getSavedVariable('nearby_beacons'));


        /* config parameters */
/*        $beaconinfo = ThirdpartyServices::getEstimoteDeviceInfo('f2f38bd72367',$appid.':'.$token);
        $this->renderBeacon($beaconinfo);*/

/*        if($this->getSavedVariable('nearby_beacons')){
            $json = json_decode($this->getSavedVariable('nearby_beacons'),true);

            if(is_array($json['beacons'])){

                foreach ($json['beacons'] as $beacon){
                    $fullid = $beacon['beacon_id'] .':' .$beacon['beacon_major']  .':' .$beacon['beacon_minor'];
                    $beaconinfo = ThirdpartyServices::getEstimoteDeviceInfo($fullid,$token);
                    $this->data->scroll[] = $this->getText(json_encode($beaconinfo));
                    $this->data->scroll[] = $this->getSpacer('20');
                }
            }
        }*/


        //$this->data->scroll[] = $this->getText($this->getSavedVariable('nearby_beacons'));
        //$this->data->scroll[] = $this->getExampleString();


        return $this->data;
    }

    public function setFooter(){
        $permission = new StdClass();
        $permission->action = 'ask-location';

        $empty = new StdClass();
        $empty->action = 'submit-form-content';
        $empty->id = 'empty-beacons';

        $onclick = new StdClass();
        $onclick->action = 'find-beacons';
        $onclick->sync_open = 1;
        $onclick->region = new StdClass();
        $onclick->region->beacon_id = $this->region;

        $this->data->footer[] = $this->getTextbutton('{#scan_for_beacons#}',array('id' => 'beaconscan','onclick' => array($empty,$permission,$onclick)));

    }
    public function renderBeacon($data){

        $onclick = new stdClass();
        $onclick->action = 'submit-form-content';
        $onclick->id = 'openbeacon_'.$data['identifier'];

        $obj = MobilebeaconadminModel::model()->findByAttributes(array('identifier' => $data['identifier']));
        $address = isset($obj->street_name) ? $obj->street_name : 'n/a';
        $name = $this->obj_beaconmodel->getBeaconName($data);

        $col[] = $this->getImage($data['color'].'.png',array('padding' => '10 10 10 10','font-size' => '12'));

        if($name == 'Unnamed Beacon'){
            $row[] = $this->getText($this->obj_beaconmodel->getBeaconName($data),array('padding' => '0 10 0 10','font-size' => '13','color' => '#ff165d'));
        } else {
            $row[] = $this->getText($this->obj_beaconmodel->getBeaconName($data),array('padding' => '0 10 0 10','font-size' => '13'));
        }

        $row[] = $this->getText($address,array('padding' => '0 10 0 10','font-size' => '12'));
        $row[] = $this->getText('{#battery#}: ' .$data['status_report']['battery_percentage'].'%',array('padding' => '0 10 0 10','font-size' => '12'));
        $row[] = $this->getText('{#firmware#}: ' .$data['status_report']['firmware_version'],array('padding' => '0 10 0 10','font-size' => '12'));
        $col[] = $this->getColumn($row);

        if($this->obj_beaconmodel->isOnline($data)){
            $col[] = $this->getText('{#nearby#}',array('padding' => '10 10 10 10','font-size' => '12'));
            $this->output->online[] = $this->getRow($col,array('padding' => '10 10 10 10','background-color' => '#c9f5bd','onclick' => $onclick));
        } else {
            $this->output->offline[] = $this->getRow($col,array('padding' => '10 10 10 10','background-color' => '#ffffff','onclick' => $onclick));
        }
    }

    public function tab2(){
        $this->data = new StdClass();
        $this->setHeader();
        $this->data->scroll[] = $this->getText('Hello World 2!');
        return $this->data;
    }

    public function setHeader(){
        $this->data->header[] = $this->getTabs(array('tab1' => '{#beacons#}','tab2' => 'List','tab3' => '{#my_devices#}'));
    }


}