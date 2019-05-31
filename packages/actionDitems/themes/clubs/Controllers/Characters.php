<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionDitems\themes\clubs\Controllers;

use packages\actionDitems\Models\ItemModel;
use packages\actionDitems\themes\clubs\Models\ExpertModel;
use packages\actionDitems\Views\View as ArticleView;
use packages\actionDitems\themes\clubs\Models\Model as ArticleModel;
use SimpleEmailServiceMessage;

class Characters extends \packages\actionDitems\Controllers\Controller {

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

        $data['characters'] = $this->model->getCharacters();
        $data['header'] = $this->model->getConfigParam('description');
        $data['subject'] = $this->model->getConfigParam('subject');

        return ['Characters',$data];
    }


}