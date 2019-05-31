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

class Fanclubs extends \packages\actionDitems\Controllers\Controller {

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

        if (strstr($this->getMenuId(), 'dislike_item_')) {
            $itemId = str_replace('dislike_item_', '', $this->getMenuId());
            // $this->model->dislikeFanclub($itemId);
        }

        if (strstr($this->getMenuId(), 'like_item_')) {
            $itemId = str_replace('like_item_', '', $this->getMenuId());
            $this->model->likeFanclub($itemId);
        }

        $data = array();
        $data['fanclubs'] = $this->model->getFanclubs();
        return ['Fanclubs', $data];
    }

}