<?php

namespace packages\actionMswipematch\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMswipematch\Views\View as ArticleView;
use packages\actionMswipematch\Models\Model as ArticleModel;

class Locationselector extends Controller
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
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

    public function actionDefault()
    {
        $data = array();
        return ['Locationselector', $this->data];
    }

    public function actionUpdateaddress()
    {

        $address = $this->model->getSubmittedVariableByName('address');
        $address = @json_decode($address,true);

        if(isset($address['lat']) AND isset($address['lon'])){
            $this->model->saveVariable('lat', $address['lat']);
            $this->model->saveVariable('lon', $address['lon']);
            $this->model->setUserAddress(true);
        }

        $this->no_output = true;
        return true;
    }

}