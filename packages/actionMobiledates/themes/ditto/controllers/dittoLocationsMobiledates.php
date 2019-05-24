<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.article.controllers.*');

Yii::import('application.modules.aelogic.packages.actionMobiledates.models.*');

class dittoLocationsMobiledates extends dittoMobiledatesSubController {

    public function tab1(){

        $this->data = new stdClass();

        $this->getLocationSubView();

        return $this->data;
    }

    public function getLocationSubView() {
        $value = '';
        if ( isset($this->submitvariables['searchterm-location']) AND !empty($this->submitvariables['searchterm-location']) ) {
            $value = $this->submitvariables['searchterm-location'];
        }

        $args = array(
            'style' => 'search-filter-location',
            'hint' => '{#free_address_search#}',
            'submit_menu_id' => 'searchbox-location',
            'variable' => 'searchterm-location',
            'id' => 'something',
        );

        $this->data->header[] = $this->getRow(array(
            $this->getFieldtext($value, $args),
            $this->getTextbutton('Search', array( 'id' => 'do-google-search', 'style' => 'google-search-btn' )),
        ), array( 'background-color' => $this->color_topbar));

        // Sponsored Locations would be shown if present
        if ( !$value ) {
            $this->displaySponsoredLocations();
        }

        $this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));

        if ( $this->menuid == 'searchbox-location' OR $this->menuid == 'do-google-search' ) {
            if(isset($this->submitvariables['searchterm-location']) AND strlen($this->submitvariables['searchterm-location']) > 0){
                $searchterm = $this->submitvariables['searchterm-location'];

                $places_module = new ArticlePlacesSearch( $this->places_key );

                if ( isset($this->varcontent['lat']) AND isset($this->varcontent['lon']) ) {
                    $places_module->location = array( $this->varcontent['lat'], $this->varcontent['lon'] );
                } else {
                    $places_module->location = array( '51.5074', '0.1278' ); // London
                }
                
                $places_module->radius = 50000;
                
                $places_module->rankby = 'distance';
                $places_module->keyword = urlencode($searchterm); // Requires keyword, name or types

                $data = $places_module->nearbySearch();
                // $data = $places_module->radarSearch();

                if ( empty($data['results']) ) {
                    $this->data->scroll[] = $this->getText('Unfortunately we didn\'t find any locations, which match your criteria. You would still be able to use your current input.', array( 'style' => 'search-result' ));
                    $this->data->scroll[] = $this->getText($searchterm, array( 'style' => 'search-result-large', 'onclick' => $this->closeLocationPopup( $searchterm ) ));
                } else {

                    foreach ($data['results'] as $result) {
                        $args = array();

                        $word = $result['name'];
                        $loc = '';
                        if ( isset($result['vicinity']) ) {
                            $word .= ', ' . $result['vicinity'];
                            $loc = $result['vicinity'];
                        }

                        $place_loc = ( $loc ? $loc : '' );

                        $onclick = $this->closeLocationPopup( $word, $place_loc );

                        $args[] = $this->getText($result['name'], array( 'style' => 'text-location-name' ));

                        if ( isset($result['rating']) ) {
                            $args[] = $this->getImage('star-pink.png', array( 'style' => 'text-location-star' ));
                            // $args[] = $this->getImage('star-red.png', array( 'style' => 'text-location-star' ));
                            $args[] = $this->getText($result['rating'], array( 'style' => 'text-location-rating' ));
                        }
                        $this->data->scroll[] = $this->getRow($args, array( 'style' => 'location-info-row', 'onclick' => $onclick ));

                        if ( isset($result['vicinity']) ) {
                            $this->data->scroll[] = $this->getText($result['vicinity'], array( 'style' => 'text-vicinity', 'onclick' => $onclick ));
                        }

                        if ( isset($result['geometry']['location']) ) {
                            $lc_params = $result['geometry']['location'];
                            $distance = $this->calculateDistance( $lc_params['lat'], $lc_params['lng'] );
                            $distance = $this->getDistance( $distance );
                            $this->data->scroll[] = $this->getText($distance, array( 'style' => 'text-distance', 'onclick' => $onclick ));
                        }

                        $this->data->scroll[] = $this->getText('', array( 'style' => 'row-divider' ));
                    }

                }

            }
        }
        
        $onclick = new stdClass();
        $onclick->action = 'close-popup';
        $onclick->keep_user_data = 1;
        $this->data->footer[] = $this->getText('Close popup', array( 'style' => 'date-button', 'onclick' => $onclick ));
    }

    /*
    * Sponsored Locations
    * Would be displayed in regard to the user's current location
    */
    public function displaySponsoredLocations() {

        $locations = $this->getConfigParam( 'geocoded_locations' );

        $max_distance = 100;

        if ( empty($locations) OR !isset($this->varcontent['lat']) ) {
            return false;
        }

        $user_lat = $this->varcontent['lat'];
        $user_lng = $this->varcontent['lon'];

        $locations_sorted = array();

        foreach ($locations as $i => $location) {
            $date_lat = $location->lat;
            $date_lng = $location->lon;
            $distance = Helper::getDistance( $user_lat, $user_lng, $date_lat, $date_lng, 'K' );

            if ( $distance > $max_distance ) {
                continue;
            }

            $locations_sorted[$i] = $distance;
        }

        natsort($locations_sorted);

        foreach ($locations_sorted as $j => $lc) {
            $location = $locations[$j];

            $args[] = $this->getText($location->place, array( 'style' => 'text-location-name-bold' ));
            $this->data->scroll[] = $this->getRow($args, array(
                'style' => 'location-info-row',
                'onclick' => $this->closeLocationPopup( $location->place )
            ));
            $this->data->scroll[] = $this->getText($this->getDistance( $lc ), array(
                'style' => 'text-distance',
                'onclick' => $this->closeLocationPopup( $location->place )
            ));
            $this->data->scroll[] = $this->getText('', array( 'style' => 'row-divider' ));

            unset($args);
        }

    }

    public function closeLocationPopup( $value, $full_address =  false ) {
        $onclick = new StdClass();
        $onclick->action = 'close-popup';
        $onclick->keep_user_data = 1;

        $update_params = array(
            $this->getVariableId('activity_address') => $value
        );

        if ( $full_address ) {
            $update_params[$this->getVariableId('activity_address_filter')] = $full_address;
        }

        $onclick->set_variables_data = (object)$update_params;

        return $onclick;
    }

}