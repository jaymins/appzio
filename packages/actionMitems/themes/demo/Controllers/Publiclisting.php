<?php

namespace packages\actionMitems\themes\demo\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\Models\Model as ArticleModel;

class Publiclisting extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    /**
     * Default action entry point.
     *
     * @return array
     */
    public function actionDefault()
    {
        $this->model->setBackgroundColor();
        $page = isset($_REQUEST['next_page_id']) ? $_REQUEST['next_page_id'] : 1;
        $items = $this->model->getAllItems($page);
        $bookmarks = $this->model->getMyLikedItemsArray();

        $this->model->filterMenu();


        return ['Publiclisting', array(
            'items' => $items,
            'bookmarks' => $bookmarks
        )];
    }

    public function actionSavefavorite(){
        $this->model->selectItemStatus($this->getMenuId(), 'like');
        $this->no_output = true;
    }

    public function actionDelfavorite(){
        $this->model->removeFromLiked($this->getMenuId());
        $this->no_output = true;
    }
}
