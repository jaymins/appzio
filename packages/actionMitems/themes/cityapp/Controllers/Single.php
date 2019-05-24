<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\cityapp\Controllers;

use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\themes\cityapp\Models\Model as ArticleModel;

class Single extends \packages\actionMitems\Controllers\Controller {

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
	    $data = [];

	    // $this->model->rewriteActionConfigField('hide_menubar', 1);
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $item_id = $this->getMenuId();

        if ( $item_id ) {
            $this->model->sessionSet('current_item', $item_id);
        } else {
            $item_id = $this->model->sessionGet('current_item');
        }

        $data['art_item'] = $this->model->getItem($item_id);

        return ['Single', $data];
    }

}