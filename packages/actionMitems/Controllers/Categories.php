<?php

namespace packages\actionMitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\Models\ItemCategoryModel;
use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\Models\Model as ArticleModel;

class Categories extends BootstrapController
{

    /**
     * @var ArticleView
     */
    public $view;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     *
     * @return array
     */
    public function actionDefault()
    {

        $data['categories'] = $this->model->getHierarchicalCategoryList();
        return ['Categories', $data];
    }

    /**
     * Action triggered when a user likes/dislikes a item.
     * He is afterwards returned to the single item view.
     *
     * @return array
     */
    public function actionSelect()
    {
        $this->model->rewriteActionConfigField('background_color', '#1b1b1b');

        $itemId = $this->model->sessionGet('item_id');
        $status = $this->getMenuId();

        $this->model->selectItemStatus($itemId, $status);

        $item = $this->model->getItem($this->model->sessionGet('item_id'));
        $isLiked = $status === 'like' ? true : false;
        $close = $status === 'like' ? false : true;

        return ['View', [
            'item' => $item,
            'isLiked' => $isLiked,
            'close' => $close
        ]];
    }

    /**
     * Remove a item from liked list
     *
     * @return array
     */
    public function actionRemove()
    {
        $itemId = $this->model->sessionGet('item_id_' . $this->model->action_id);
        $this->model->removeFromLiked($itemId);

        $item = $this->model->getItem($itemId);

        return ['View', [
            'item' => $item,
            'isLiked' => false
        ]];
    }

    /**
     * Match the current user with given artist.
     * By doing this they will be available in each other's Messages menu.
     *
     */
    public function actionMatch()
    {
        $itemId = $this->model->sessionGet('item_id');
        $item = $this->model->getItem($itemId);
        $artistId = $this->getMenuId();

        if (!$artistId) {
            return ['View', [
                'item' => $item
            ]];
        }

        $storage = new \AeplayKeyvaluestorage();
        $storage->play_id = $this->model->playid;

        $exists = $storage->valueExists('two-way-matches', $artistId);

        if (!$exists) {
            $storage->set('two-way-matches', $artistId);
            $storage->play_id = $artistId;
            $storage->set('two-way-matches', $this->model->playid);
        }

        return ['View', [
            'item' => $item,
            'isLiked' => true
        ]];
    }

    public function actionSaveCategory(){
        $this->model->saveCategory();
        $this->no_output = true;
    }

}
