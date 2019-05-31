<?php

namespace packages\actionDitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Models\Model as ArticleModel;

class Location extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    /**
     * Default action entry point.
     *
     * @return array
     */
    public function actionDefault()
    {
        $term = null;

        if ($this->getMenuId() == 'search') {
            $term = $this->model->getSubmittedVariableByName('term');
        }

        $apikey = 'AIzaSyBOh1EACK1VOkmLVjx50RAEP-D7XNjIhiE';

        $places_module = new \ArticlePlacesSearch($apikey);

        if (isset($this->model->varcontent['lat']) AND isset($this->model->varcontent['lon'])) {
            $places_module->location = array($this->model->varcontent['lat'], $this->model->varcontent['lon']);
        } else {
            $places_module->location = array('51.5074', '0.1278'); // London
        }

        $places_module->radius = 50000;
        $places_module->rankby = 'distance';
        $places_module->types = 'geocode';


        if (stristr($this->getMenuId(), 'save-address-')) {
            $address = str_replace('save-address-', '', $this->getMenuId());
            $this->model->saveVariable('address', $address);

            $places_module->input = urlencode($address);
            $data = $places_module->autocomplete();

            $places_module->placeid = $data['predictions'][0]['place_id'];

            $details = $places_module->details();
            $this->model->saveVariable('lat', $details["result"]["geometry"]["location"]["lat"]);
            $this->model->saveVariable('lon', $details["result"]["geometry"]["location"]["lng"]);

            if ($this->model->getConfigParam('location_items_update')) {
                $this->model->updateItemsLocation(
                    $this->model->getSavedVariable('lat'),
                    $this->model->getSavedVariable('lon')
                );
            }

            return ['Location', array(
                'complete' => true,
                'predictions' => array()
            )];
        }


        $places_module->input = urlencode($term);

        $data = $places_module->autocomplete();

        return ['Location', array(
            'predictions' => $data['predictions']
        )];
    }
}