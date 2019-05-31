<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionDitems\themes\fans\Controllers;

use packages\actionDitems\Models\ItemModel;
use packages\actionDitems\themes\fans\Models\ExpertModel;
use packages\actionDitems\Views\View as ArticleView;
use packages\actionDitems\themes\fans\Models\Model as ArticleModel;
use SimpleEmailServiceMessage;

class Controller extends \packages\actionDitems\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault()
    {

        $data = array();
        $this->collectLocation();

        // At this point we should already have the user's coordinates
        if ( $this->model->getSavedVariable('lat') ) {
            $data = \ThirdpartyServices::geoAddressTranslation(
                $this->model->getSavedVariable('lat'),
                $this->model->getSavedVariable('lon'), $this->model->appid
            );

            if ( isset($data['country']) ) {
                $this->model->saveVariable('country', $data['country']);
            }

            if ( isset($data['city']) ) {
                $this->model->saveVariable('city', $data['city']);
            }

            if ( isset($data['street']) ) {
                $this->model->saveVariable('street', $data['street']);
            }
        }

        $data['city'] = $this->model->getSavedVariable('city');
        $data['events'] = $this->model->getEvents();
        $data['my_events'] = $this->model->getMyEvents();

        return ['Home', $data];
    }

}