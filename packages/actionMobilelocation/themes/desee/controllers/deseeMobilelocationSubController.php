<?php

class deseeMobilelocationSubController extends MobilelocationController {

    public function tab1(){
        $this->data = new StdClass();

        $this->getHeader();

        $imgstyle['crop'] = 'round';
        $imgstyle['margin'] = '20 90 10 90';
        $imgstyle['text-align'] = 'center';
        $imgstyle['variable'] = $this->getVariableId('profilepic');

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage('m_change_location.png', array(
                'width' => '70',
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '15 0 15 0',
        ));

        if ( isset($this->varcontent['real_name']) ) {
            $this->data->scroll[] = $this->getText('Hi ' . $this->varcontent['real_name'] . ',', array(
                'font-size' => '22',
                'text-align' => 'center',
            ));
        }

        $this->getLocation();

        if(isset($this->menuid) AND $this->menuid == 'gps-update') {
            Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
            MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);
            $this->loadVariableContent(true);

            $this->data->scroll[] = $this->getText('{#location_updated#}', array(
                'text-align' => 'center',
                'font-size' => '15',
                'margin' => '0 0 15 0',
                'color' => '#42bb43',
            ));
            $this->saveVariable('country_selected',$this->getSavedVariable('country'));
            $this->saveVariable('city_selected',$this->getSavedVariable('city'));

            $load = new StdClass();
            $load->action = 'submit-form-content';
            $this->data->onload[] = $load;
        }

        $onclick1 = new StdClass();
        $onclick1->action = 'ask-location';
        $onclick1->sync = true;

        $onclick2 = new StdClass();
        $onclick2->action = 'submit-form-content';
        $onclick2->id = 'gps-update';

        $buttonparams['style'] = 'desee-location-button';
        $buttonparams['onclick'] = array($onclick1,$onclick2);

        if(isset($this->varcontent['lat']) AND $this->varcontent['lat']){
            MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);
        }

        $this->loadVariableContent(true);
        $this->getText($this->getSavedVariable('city'));

        $this->data->scroll[] = $this->getText('{#update_my_location_using_gps#}',$buttonparams);

        $this->getManualUpdate();

        if ( $this->getConfigParam('google_adcode_banners') ) {
            $this->data->footer[] = $this->getBanner($this->getConfigParam('google_adcode_banners'));
        }

        return $this->data;
    }

    public function manualSave($id,$txt){
        $onclick3 = new StdClass();
        $onclick3->action = 'submit-form-content';
        $onclick3->id = $id;
        $btn['onclick'] = $onclick3;
        $btn['style'] = 'desee-location-button';
        return $this->getText($txt, $btn);
    }

    public function getHeader() {

        $toggleSidemenu = new stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $this->data->header[] = $this->getRow(array(
            $this->getImage('ic_menu_new.png', array(
                'width' => '20',
                'onclick' => $toggleSidemenu
            )),
            $this->getText('{#change_location#}', array(
                'color' => '#ff6600',
                'width' => '90%',
                'text-align' => 'center',
            ))
        ), array(
            'background-color' => '#FFFFFF',
            'padding' => '10 20 10 20',
            'width' => '100%',
        ));
        $this->data->header[] = $this->getImage('header-shadow-white.png', array(
            'imgwidth' => '1440',
            'width' => '100%',
        ));

        return true;
    }

    private function getLocation() {

        if ( !isset($this->varcontent['lat']) OR !isset($this->varcontent['lon']) ) {
            return false;
        }

        if ( !isset($this->varcontent['city']) OR !isset($this->varcontent['country'])  ) {
            $this->data->scroll[] = $this->getText('Unable to locate properly :(', array(
                'font-size' => '22',
                'text-align' => 'center',
            ));
        } else {
            $this->data->scroll[] = $this->getRow(array(
                $this->getText('Your Location is ', array(
                    'font-size' => '19',
                )),
                $this->getText($this->varcontent['city'] . ', ', array(
                    'color' => '#ff6600',
                    'font-size' => '19',
                )),
                $this->getText($this->varcontent['country'], array(
                    'color' => '#ff6600',
                    'font-size' => '19',
                )),
            ), array(
                'text-align' => 'center',
                'margin' => '0 0 10 0',
            ));
        }

        return true;
    }

    private function getManualUpdate() {

        if ( !$this->getConfigParam('enable_manual_location') ) {
            return false;
        }

        $this->data->scroll[] = $this->getSpacer(10);

        $this->data->scroll[] = $this->getImage('desee-location-or.png', array(
            'width' => '100%',
            'margin' => '0 20 10 20',
        ));

        $this->data->scroll[] = $this->getText('{#change_location_manually#}', array(
            'font-size' => '22',
            'text-align' => 'center',
        ));

        if ( $this->menuid == 'update-location' AND isset($this->submitvariables['location_to_update']) ) {
            $new_location = $this->submitvariables['location_to_update'];


            $apikey = Aemobile::getConfigParam($this->gid, 'google_maps_api');

            if (!$apikey) {
                $apikey = 'AIzaSyBOh1EACK1VOkmLVjx50RAEP-D7XNjIhiE';
            }

            $maps_module = new ArticleGmapsSearch($apikey);
            $maps_module->search_param = $new_location;
            $results = $maps_module->getInfoLocation();
            $address = $maps_module->getAddress();
            $coords = $maps_module->getCoords();

            $city = $maps_module->getAddressComponent('locality');
            $country = $maps_module->getAddressComponent('country');

            $data = $maps_module->getData();

            if ( $data ) {
                $this->saveVariable('city', $city['long_name']);
                $this->saveVariable('country', $country['long_name']);

                Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
                MobilelocationModel::addressTranslation($this->gid, $this->playid, $country['long_name'], $city['long_name']);

                $this->data->onload[] = $this->getOnclick( 'submit' );
                return true;
            }

        }

        $this->data->scroll[] = $this->getSpacer(20);

        $this->data->scroll[] = $this->getFieldtext('', array(
            'variable' => 'location_to_update',
            'margin' => '10 20 10 20',
            'padding' => '0 10 0 10',
            'color' => '#8e8e8e',
            'hint' => 'Enter location',
            'border-width' => '1',
            'border-color' => '#e0e0e0',
            'border-radius' => '5',
            'height' => '50',
            'vertical-align' => 'middle',
        ));


        $this->data->scroll[] = $this->manualSave('update-location','{#update_location#}');

        return true;
    }

}