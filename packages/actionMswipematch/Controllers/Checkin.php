<?php

namespace packages\actionMswipematch\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMswipematch\Models\Model;
use packages\actionMswipematch\Views\View as ArticleView;
use packages\actionMswipematch\Models\Model as ArticleModel;

class Checkin extends Controller {

    /* @var ArticleView */
    public $view;

    /* @var Model */
    public $model;

    public $mobilematchingobj;
    // public $current_playid;
    // public $current_gid;
    public $actionid;
    public $current_id;

    public $its_a_match = false;

    public $skip_submit;
    public $data;
    public $update_location;


    /* overall logic for the checkin:
    - clicking on a checkin button from the list, calls actionGoogleplacecheckin
    - clicking on search triggers a google place search after which action will update and
        venue_temp variable will be present
    - clicking check in next to the search, will trigger action checkin
    - clicking on search will update location & after delay call action update
    */

    public function actionDefault(){
        $data = array();

        if($this->getMenuId() == 'googlesearch'){
            $this->model->setPlaceSearch($this->skip_submit);
        } else {
            $this->model->setCheckin($this->skip_submit);
        }

        $this->data['icon_color'] = $this->model->getConfigParam('icon_colors') ? $this->model->getConfigParam('icon_colors') : 'black';

        $this->data['address'] = $this->model->placeaddress;
        $this->data['place'] = $this->model->placename;
        $this->data['lat'] = $this->model->lat;
        $this->data['lon'] = $this->model->lon;
        $this->data['checked_in'] = $this->model->checked_in;

        $this->data['country'] = $this->model->getSavedVariable('country');
        $this->data['city'] = $this->model->getSavedVariable('city');;
        $this->data['countries'] = '';
        $this->data['cities'] = '';

        $this->data = $this->configureTopMenu($this->data);

        if($this->getMenuId() == 'selectcity'){
            $this->model->setNewAddress();
        }

        if($this->getMenuId() == 'selectcountry'){
            $country = $this->model->getSubmittedVariableByName('country_selected');
            $this->data['country'] = $country;
            $this->data['cities'] = $this->model->getCityList($country);
            $this->model->saveVariable('temp_country', $country);
        } else {
            $this->data['countries'] = $this->model->getCountryList();
        }

        /* this lat & lon is used to show the map */
        if(!$this->data['lat'] OR !$this->data['lon']){
            /* if its not update location command & model has place's location defined we use that */
            if(!$this->update_location AND $this->model->lat AND $this->model->lon){
                $this->data['lat'] = $this->model->lat;
                $this->data['lon'] = $this->model->lon;
            }
        }

        if(!$this->data['lat'] OR !$this->data['lon']){
            /* if all else fails, we show users current location */
            $this->data['lat'] = $this->model->getSavedVariable('lat');
            $this->data['lon'] = $this->model->getSavedVariable('lon');
        }

        $this->data['my_address'] = $this->model->getMyAddress();
        $this->data['places_nearby'] = $this->model->getPlacesNearby();
        $this->data['update_location'] = $this->model->checkForUpdateLocation();

        return ['Checkin',$this->data];
    }

    public function actionUpdatelocation(){
        $this->data['lat'] = $this->model->getSavedVariable('lat');
        $this->data['lon'] = $this->model->getSavedVariable('lon');
        $this->update_location = true;
        return self::actionDefault();
    }

    public function actionCheckin(){
        $this->model->sessionSet('checked_in', true);
        $this->model->checkinToVenue();
        return self::actionDefault();
    }

    public function actionGooleplacecheckin(){
        $id = $this->getMenuId();
        $this->model->sessionSet('checked_in', true);
        $this->model->checkinToVenue($id);
        $this->skip_submit = true;
        return self::actionDefault();
    }


}