<?php

namespace packages\actionMproducts\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMproducts\Views\View as ArticleView;
use packages\actionMproducts\Models\Model as ArticleModel;

class Controller extends BootstrapController {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public $product_info;


    /**
     * This is the default action inside the controller. This gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault(){

        /* if user has already completed the first phase, move to phase 2 */

        $data['fieldlist'] = $this->model->getFieldlist();
        //$data['current_country'] = $this->model->getCountry();
        $this->model->configureMenu();

        /* if user has clicked the signuop, we will first validate
        and then save the data. validation errors are also available to views and components. */
        if($this->getMenuId() == 'signup'){
            //$this->model->validatePage1();

            if(empty($this->model->validation_errors)){
                /* if validation succeeds, we save data to variables and move user to page 2*/
                //$this->model->savePage1();
                $this->model->sessionSet('reg_phase', 2);
                return ['Pagetwo',$data];
            }
        }

        return ['View',$data];
    }
}
