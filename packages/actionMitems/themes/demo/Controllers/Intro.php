<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\demo\Controllers;

use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\themes\demo\Models\Model as ArticleModel;

class Intro extends \packages\actionMitems\Controllers\Controller {

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
