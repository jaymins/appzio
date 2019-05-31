<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionDitems\themes\fans\Controllers;

use packages\actionDitems\Models\ItemModel;
use packages\actionDitems\Views\View as ArticleView;
use packages\actionDitems\themes\fans\Models\Model as ArticleModel;
use SimpleEmailServiceMessage;

class Home extends \packages\actionDitems\Controllers\Controller {

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
        $data['city'] = $this->model->getSavedVariable('city');

        return ['Home', $data];
    }

}