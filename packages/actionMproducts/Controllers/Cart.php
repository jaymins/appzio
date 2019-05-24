<?php

namespace packages\actionMproducts\Controllers;
use Bootstrap\Controllers\BootstrapController;
use function is_int;
use function is_numeric;
use packages\actionMproducts\Views\View as ArticleView;
use packages\actionMproducts\Models\Model as ArticleModel;

class Cart extends Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public $product_id;
    public $product_info;

    public $data;

    /**
     * This is the default action inside the controller. This gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault(){
        $this->configureBasics();

        if($this->data['isParent'] == 1){
            if($this->model->getItemId()){
                $this->data['active_product'] = $this->model->getItemId();
                $this->data['active_product_info'] = $this->model->getProductInfo($this->model->getItemId());
            } else {
                $this->data['active_product'] = 0;
            }

            return ['parentcart',$this->data];
        } else {
            return ['cart',$this->data];
        }
    }

    /**
     * @param bool $menu
     * @return void
     */
    public function configureBasics($menu=true){
        $this->product_id = $this->model->getProductId();
        $this->product_info = $this->model->getProductInfo();
        //$this->model->rewriteActionConfigField('backarrow', 1);
        $title = $this->model->localize('{#my_cart#}');
        $this->model->rewriteActionField('subject', $title);
        $this->model->addRemoveFromCart();
        $this->model->configureMenu();


        $actionName = $this->model->getCurrentActionPermaname();
        $this->data['isPopup'] = 0;
        if ($actionName == 'cartpopup') {
            $this->data['isPopup'] = 1;
        }

        /* we are resetting the action creation to its default state here */
        $this->model->flushActionRoutes(false,'addtask');

        $this->data['product_info'] = $this->product_info;
        $this->data['product_id'] = $this->product_id;
        $this->data['add'] = $this->getMenuId() .'wrong function' .$this->router->getControllerName();
        if($this->model->getSavedVariable('role') == 'parent') {
            $this->data['cart'] = $this->model->getParentCart();
            $this->data['isParent'] = 1;
            $this->data['cart_total'] = $this->model->getParentCartTotal();
            $this->data['cart_items'] = $this->model->getParentCartItems();

        } else {
            $this->data['cart'] = $this->model->getCart();
            $this->data['isParent'] = 0;
            $this->data['cart_total'] = $this->model->getCartTotal();
            $this->data['cart_items'] = $this->model->getCartItems();
        }

    }

    /**
     * @return array
     */
    public function actionAddtocart(){

        if(is_numeric($this->model->getMenuId())){
            $this->model->addToCart((int)$this->model->getItemId());
        }

        $this->configureBasics(false);
        $this->model->bottom_menu_id = false;

        return ['cart',$this->data];
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

    /**
     * @return void
     */
    public function actionConfirmbuy() {
        $this->model->confirmParentBuy();
        $this->model->flushActionRoutes();
        $this->no_output = true;
    }

    /**
     * @return void
     */
    public function actionConfirmproduct() {
        $cart = $this->model->getParentCart();
        $id = $this->model->getItemId();
        $this->model->buyProduct($id,$cart);
        $this->no_output = true;
    }

}
