<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\fans\Controllers;

use packages\actionMitems\Models\ItemModel;
use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\themes\fans\Models\Model as ArticleModel;
use SimpleEmailServiceMessage;

class Showevent extends \packages\actionMitems\Controllers\Controller {

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
        $this->collectLocation();
        $data = array();

        if($this->getMenuId()){
            $data['event'] = $this->model->getEvent($this->getMenuId());
            $data['participants'] = $this->model->getParticipants($this->getMenuId());
        }

        return ['Showevent', $data];
    }

    public function actionGoing(){

        if(!$this->getMenuId()){
            return ['Showevent', []];
        }

        $id = $this->getMenuId();

        $this->model->going($id);
        $data['event'] = $this->model->getEvent($id);
        $data['participants'] = $this->model->getParticipants($id);

        return ['Showevent', $data];


    }

}