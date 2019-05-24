<?php

namespace packages\actionMproducts\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMproducts\Views\View as ArticleView;
use packages\actionMproducts\Models\Model as ArticleModel;

class Categorylist extends Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    /**
     * This is the default action inside the controller. This gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault(){

        $this->model->rewriteActionConfigField('hide_menubar', 1);

        if($this->getMenuId() == 'search_term'){
            $this->model->configureMenu();
            $data['searchterm'] = $this->model->getSubmittedVariableByName('searchterm');
            $data['products'] = $this->model->searchProducts();
            $data['search_back'] = 'main';
            return ['searchresults',$data];
        }

        $data['categories'] = $this->model->getCategories();
        $data['featured'] = $this->model->getFeaturedProducts();
        $this->model->configureMenu();
        return ['categorylist',$data];
    }
}
