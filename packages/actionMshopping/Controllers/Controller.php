<?php

namespace packages\actionMshopping\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMshopping\Views\View as ArticleView;
use packages\actionMshopping\Models\Model as ArticleModel;

class Controller extends BootstrapController
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
        $item = null;
        $isLiked = null;
        $id = null;

        // If a menuid is passed in any way, update the item id stored in the session
        $id = $this->model->getItemId();

        // TODO: invert logic here and throw error instead of having logic in conditional statement
        if ($id AND is_numeric($id)) {
            $this->model->sessionSet('booking_item_id', $id);
            $item = $this->model->getItem($id);
            $isLiked = $this->model->isItemLiked($id);
            $isBooked = $this->model->isItemBooked($id);
        }

        return ['View', compact('item', 'isLiked', 'isBooked')];
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

        $this->no_output = true;
    }
}
