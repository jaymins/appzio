<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionDitems\themes\demo\Controllers;

use packages\actionDitems\Views\View as ArticleView;
use packages\actionDitems\themes\demo\Models\Model as ArticleModel;

class Intro extends \packages\actionDitems\Controllers\Controller {

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
        return ['Intro', $data];
    }



}
