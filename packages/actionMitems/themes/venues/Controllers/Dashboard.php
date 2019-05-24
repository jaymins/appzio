<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\venues\Controllers;

use packages\actionMitems\Models\ItemModel;
use packages\actionMitems\themes\venues\Models\ExpertModel;
use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\themes\venues\Models\Model as ArticleModel;
use SimpleEmailServiceMessage;

class Dashboard extends \packages\actionMitems\Controllers\Controller {

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

        $data['table'] = $this->model->getDashboard();
        $data['header'] = $this->model->getConfigParam('description');
        $data['subject'] = $this->model->getConfigParam('subject');

        return ['Dashboard',$data];
    }


}