<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\cityapp\Controllers;

use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\themes\cityapp\Models\Model as ArticleModel;

class Search extends \packages\actionMitems\Controllers\Controller {

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

        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $current_action = $this->model->getItemId();

        $searchterm = $this->model->getSubmittedVariableByName('searchterm');

        if ( $current_action === 'search' AND !empty($searchterm) ) {

            $data['searchterm'] = $searchterm;
            $data['search_items'] = $this->model->getSearchItems( $searchterm );

            return ['Searchlist', $data];
        }

        $category_list = $this->model->getItemsByCategory();

        $data['category_list'] = $category_list;

        return ['Search', $data];
    }

}