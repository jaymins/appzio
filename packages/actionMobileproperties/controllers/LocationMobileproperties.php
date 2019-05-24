<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');

class LocationMobileproperties extends MobilepropertiesController
{
    public $data;
    public $theme;
    public $grid;
    public $margin;
    public $deleting;
    /** @var MobilepropertiesModel */
    public $propertyModel;
    public $currentId;

    public function tab1()
    {
        $this->initModel();
        $this->data = new stdClass();

        $value = '';
        if (isset($this->submitvariables['searchterm-location']) AND !empty($this->submitvariables['searchterm-location'])) {
            $value = $this->submitvariables['searchterm-location'];
        }

        $args = array(
            'style' => 'search-filter-location',
            'hint' => '{#enter_a_postcode_or_an_address#}',
            'submit_menu_id' => 'searchbox-location',
            'variable' => 'searchterm-location',
            'id' => 'something',
            // 'input_type' => 'uppercase'
        );

        $this->data->header[] = $this->getRow(array(
            $this->getFieldtext($value, $args),
            $this->getTextbutton('Search', array('id' => 'do-google-search', 'style' => 'google-search-btn')),
        ), array('background-color' => $this->color_topbar));

        $this->data->scroll[] = $this->getLoader('Loading', array('color' => '#000000', 'visibility' => 'onloading'));

        if ($this->menuid == 'searchbox-location' OR $this->menuid == 'do-google-search') {
            $searchterm = $this->submitvariables['searchterm-location'];
            $apikey = Aemobile::getConfigParam($this->gid, 'google_maps_api');

            if (!$apikey) {
                $apikey = 'AIzaSyBOh1EACK1VOkmLVjx50RAEP-D7XNjIhiE';
            }

            $places_module = new ArticlePlacesSearch( $apikey );

            if ( isset($this->varcontent['lat']) AND isset($this->varcontent['lon']) ) {
                $places_module->location = array( $this->varcontent['lat'], $this->varcontent['lon'] );
            } else {
                $places_module->location = array( '51.5074', '0.1278' ); // London
            }
            
            $places_module->radius = 50000;
            $places_module->rankby = 'distance';
            $places_module->types = 'geocode';
            $places_module->input = urlencode($searchterm);

            $data = $places_module->autocomplete();

            if ( !isset($data['predictions']) OR empty($data['predictions']) ) {
                $this->data->scroll[] = $this->getText('{#looks_like_your_address_is_not_valid#}. {#you_can_still_use_it#}', array('style' => 'search-result', 'onclick' => $this->selectOnclick($searchterm)));
                $this->data->scroll[] = $this->getText($searchterm, array('style' => 'search-result-large', 'onclick' => $this->selectOnclick($searchterm)));
                $this->data->footer[] = $this->getText('Use this location', array('style' => 'submit-button', 'onclick' => $this->selectOnclick($searchterm)));
            } else {
                $predictions = $data['predictions'];

                foreach ($predictions as $prediction) {
                    
                    if ( !isset($prediction['description']) ) {
                        continue;
                    }

                    $predicted_address = $prediction['description'];

                    $this->data->scroll[] = $this->getRow(array(
                        $this->getText($predicted_address, array('style' => 'text-location-name-bold')),
                    ), array(
                        'style' => 'location-info-row',
                        'onclick' => $this->selectOnclick( $predicted_address ),
                    ));

                }
            }

        }

        if ($this->menuid != 'searchbox-location' AND $this->menuid != 'do-google-search') {
            $this->displayRecentSearches();
        }
        
        // $this->data->footer[] = $this->getText('Back', array( 'style' => 'back-button', 'onclick' => $onclick ));
        return $this->data;
    }

    private function displayRecentSearches() {
        $recents = $this->propertyModel->getRecentDistricts();

        if ( empty($recents) ) {
            return false;
        }

        foreach (array_reverse($recents) as $i => $region) {

            if ( !isset($region['postcode']) OR !isset($region['region']) ) {
                continue;
            }

            // Show top 3 results
            if ( $i > 2 ) {
                continue;
            }

            if ( !$i ) {
                $this->data->footer[] = $this->getText('{#recent_searches#}', array(
                    'background-color' => $this->color_topbar,
                    'color' => '#ffffff',
                    'padding' => '7 0 7 0',
                    'text-align' => 'center',
                    'font-size' => '14'
                ));
            }

            $this->data->footer[] = $this->getText($region['postcode'] . ', ' . $region['region'], array(
                'style' => 'search-result',
                'onclick' => $this->selectOnclick($region['region'])
            ));
        }

    }

    private function selectOnclick($value){
        $onclick = new StdClass();

        $onclick->action = 'close-popup';
        $onclick->keep_user_data = 1;
        $update_params = array(
            'temp_district' => $value
        );

        $onclick->set_variables_data = (object)$update_params;
        return $onclick;
    }

}