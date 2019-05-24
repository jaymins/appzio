<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\clubs\Controllers;

use packages\actionMitems\Models\ItemModel;
use packages\actionMitems\themes\clubs\Models\ExpertModel;
use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\themes\clubs\Models\Model as ArticleModel;
use SimpleEmailServiceMessage;

class Create extends \packages\actionMitems\Controllers\Controller {

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
        
        $data = [];

        if ($this->getMenuId() == 'save') {
            $this->model->saveClubItem();
        }

        return ['Create', $data];
    }

}