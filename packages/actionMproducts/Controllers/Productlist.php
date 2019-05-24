<?php

namespace packages\actionMproducts\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMproducts\Views\View as ArticleView;
use packages\actionMproducts\Models\Model as ArticleModel;

class Productlist extends Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public $category_id;
    public $category_info;

    /**
     * This is the default action inside the controller. This gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault(){
        $this->category_id = $this->model->getCategoryId();
        $this->category_info = $this->model->getCategoryInfo();
        $this->model->rewriteActionConfigField('backarrow', 1);
        $this->model->configureMenu();

        if(isset($this->category_info->title)){
            $title = $this->model->localize($this->category_info->title);
            $this->model->rewriteActionField('subject', $title);
            $data['category_name'] = $this->category_info->title;
        }

        $data['products'] = $this->model->getProducts();


        return ['productlist',$data];
    }
}
