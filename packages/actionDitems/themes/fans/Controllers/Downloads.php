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

class Downloads extends \packages\actionDitems\Controllers\Controller {

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

        $data['downloads'] = $this->model->getDownloads();
        $data['header'] = $this->model->getConfigParam('header');
        $data['subject'] = $this->model->getConfigParam('subject');

        return ['Downloads',$data];
    }


}