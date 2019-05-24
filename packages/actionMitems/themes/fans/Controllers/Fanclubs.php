<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\fans\Controllers;

use packages\actionMitems\Models\ItemModel;
use packages\actionMitems\themes\fans\Models\ExpertModel;
use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\themes\fans\Models\Model as ArticleModel;
use SimpleEmailServiceMessage;

class Fanclubs extends \packages\actionMitems\Controllers\Controller {

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