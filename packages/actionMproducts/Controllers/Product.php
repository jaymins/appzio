<?php

namespace packages\actionMproducts\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMproducts\Views\View as ArticleView;
use packages\actionMproducts\Models\Model as ArticleModel;

class Product extends Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public $product_id;
    public $product_info;

    /**
     * This is the default action inside the controller. This gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault(){
        $this->product_id = $this->model->getProductId();
        $this->product_info = $this->model->getProductInfo();
        $this->model->rewriteActionConfigField('backarrow', 1);
        $this->setProductNameToSubject();
        $this->model->configureMenu();

        $data['product_info'] = $this->product_info;
        $data['product_id'] = $this->product_id;

        return ['product',$data];
    }

    /**
     * @return void
     */
    public function setProductNameToSubject(){
        if(isset($this->product_info->title)){
            $title = $this->model->localize($this->product_info->title);
            if(strlen($title) > 32){
                $title = substr($title,0,32) .'...';
            }
            $this->model->rewriteActionField('subject', $title);
        }

    }
}
