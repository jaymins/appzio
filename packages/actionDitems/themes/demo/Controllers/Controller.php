<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionDitems\themes\demo\Controllers;

use packages\actionDitems\Views\View as ArticleView;
use packages\actionDitems\themes\demo\Models\Model as ArticleModel;

class Controller extends \packages\actionDitems\Controllers\Controller {

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
        $item = null;
        $isLiked = null;
        $id = null;

        $this->model->handleBoostPurchase();

        // If a menuid is passed in any way, update the item id stored in the session
        $id = $this->model->getItemId();

        if ($id AND is_numeric($id)) {
            $item = $this->model->getItem($id);
            $isLiked = $this->model->isItemLiked($id);
            $this->model->initMobileMatching($item->play_id);
        }

        $data['item'] = $item;
        $data['isLiked'] = $isLiked;

        return ['View', $data];
    }

    public function actionAddMatch(){
        $id = $this->model->getItemId();
        $item = $this->model->getItem($id);

        if ($id AND is_numeric($id)) {
            $this->model->initMobileMatching($item->play_id);
        }

        $this->no_output = true;
    }

    /**
     * Add a item from liked list
     *
     * @return array
     */
    public function actionUnstar()
    {

        $id = $this->model->getItemId() ? $this->model->getItemId() : $this->model->sessionGet('item_id');

        if($id){
            $this->model->removeFromLiked($id);
        }

        $this->no_output = true;
    }

    /**
     * Add a item from liked list
     *
     * @return array
     */
    public function actionStar()
    {

        $id = $this->model->getItemId() ? $this->model->getItemId() : $this->model->sessionGet('item_id');

        if($id){
            $this->model->selectItemStatus($id, 'like');
        }

        $this->no_output = true;


    }

}
