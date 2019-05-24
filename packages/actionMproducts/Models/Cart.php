<?php


namespace packages\actionMproducts\Models;
use Bootstrap\Models\BootstrapModel;
use function str_replace_array;

trait Cart {

    /* cart functionalities */

    /**
     * @return void
     */
    public function addRemoveFromCart(){

        if(stristr($this->getMenuId(),'removefromcart-')){
            $id = str_replace('removefromcart-','',$this->getMenuId());
            $obj = ProductcartsModel::model()->findByPk($id);
            if(isset($obj->id)){
                if($obj->quantity == 1){
                    $this->removeFromCart(false,$obj->id);
                } else {
                    $obj->quantity = $obj->quantity-1;
                    $obj->update();
                }
            }
        }

        if(stristr($this->getMenuId(),'addtocart-')){
            $id = str_replace('addtocart-','',$this->getMenuId());
            $obj = ProductcartsModel::model()->findByPk($id);
            if(isset($obj->id)){
                $obj->quantity = $obj->quantity+1;
                $obj->update();
            }
        }
    }

    /**
     * @param null $playId
     * @param bool $taskid
     * @return array|static[]
     */
    public function getCart($playId = null,$taskid=false){
        if ($playId == null) {
            $playId = $this->playid;
        }

        if($taskid){
            $cart = ProductcartsModel::model()->with('product')->findAllByAttributes(array('play_id' => $playId,'task_id' => $taskid));
        } else {
            $cart = ProductcartsModel::model()->with('product')->findAllByAttributes(array('play_id' => $playId,'cart_status' => 'cart'));
        }

        if($cart){
            return $cart;
        }

        return array();
    }

    /**
     * @return int
     */
    public function emptyCart(){
        return ProductcartsModel::model()->deleteAllByAttributes(array('play_id' => $this->playid,'cart_status' => 'cart'));
    }

    /**
     * @param bool $product_id
     * @param bool $item_id
     */
    public function removeFromCart($product_id=false,$item_id=false){
        if($product_id){
            @ProductcartsModel::model()->deleteAllByAttributes(array('play_id' => $this->playid,'product_id' => $product_id,'cart_status' => 'cart'));
        } elseif($item_id){
            @ProductcartsModel::model()->deleteByPk($item_id);
        }
    }

    /**
     * @param bool $product_id
     * @return bool
     */
    public function addToCart($product_id=false){


        $obj = ProductcartsModel::model()->findByAttributes(array('play_id' => $this->playid,'product_id'=>$product_id,'cart_status' => 'cart'));

        if(isset($obj->quantity)){
                    /* we no longer increment the product count
                $obj->quantity = $obj->quantity+1;
                $obj->update();
                    */
            return true;
        }

        $obj = new ProductcartsModel();
        $obj->play_id = $this->playid;
        $obj->product_id = $product_id;
        $obj->date_added = time();
        $obj->quantity = 1;

        $obj->insert();

    }

    /**
     * @param null $playId
     * @param bool $taskid
     * @return mixed
     */
    public function getCartTotal($playId = null,$taskid=false){
        if(!$this->cartdata){
            $this->setCartData($playId,$taskid);
        }

        return $this->cartdata['cart_total'];
    }

    /**
     * @param null $playId
     * @param bool $taskid
     * @return mixed
     */
    public function getCartItems($playId = null,$taskid = false){
        if(!$this->cartdata){
            $this->setCartData($playId,$taskid);
        }

        return $this->cartdata['cart_items'];
    }

    /**
     * @param null $playId
     * @param bool $taskid
     * @return mixed
     */
    public function setCartData($playId = null,$taskid=false){
        if ($playId == null) {
            $playId = $this->playid;
        }

        $array['cart_total'] = 0;
        $array['cart_items'] = 0;

        if($taskid){
            $rows = ProductcartsModel::model()->with('product')->findAllByAttributes(array('play_id' => $playId,'cart_status' => 'task', 'task_id' => $taskid));
        } else {
            $rows = ProductcartsModel::model()->with('product')->findAllByAttributes(array('play_id' => $playId,'cart_status' => 'cart'));
        }

        if(!$rows){
            $this->cartdata = $array;
            return $array;
        }

        foreach($rows as $row){
            $array['cart_total'] = $array['cart_total'] + ($row->quantity) * $row->product->price;
            $array['cart_items'] = $array['cart_items'] + $row->quantity;
        }

        $this->cartdata = $array;
    }

    /**
     * @param $taskId
     * @return mixed
     */
    public function getCartDataTask($taskId) {
        $array['cart_total'] = 0;
        $array['cart_items'] = 0;

        $rows = ProductcartsModel::model()->with('product')->findAllByAttributes(array('task_id' => $taskId));

        if(!$rows){
            return $array;
        }

        foreach($rows as $row){
            $array['cart_total'] = $array['cart_total'] + ($row->quantity) * $row->product->price;
            $array['cart_items'] = $array['cart_items'] + $row->quantity;
        }

        return $array;
    }

    /**
     * @param $taskId
     * @return array|static[]
     */
    public function getCartByTask($taskId){

        $cart = ProductcartsModel::model()->with('product')->findAllByAttributes(array('task_id' => $taskId));

        if($cart){
            return $cart;
        }

        return array();
    }

    /**
     * @param $taskId
     */
    public function updateCartShopping($taskId) {
        $rows = ProductcartsModel::model()->with('product')->findAllByAttributes(array('task_id' => $taskId));
        foreach ($rows as $row) {
            $row->cart_status = 'shopping';
            $row->update();
        }
    }


    /*$sql = "SELECT * FROM ae_ext_products_carts
                    LEFT JOIN ae_ext_mtasks ON ae_ext_products_carts.task_id = ae_ext_mtasks.id
                    LEFT JOIN ae_ext_products ON ae_ext_products_carts.product_id = ae_ext_products.id
                    WHERE ae_ext_mtasks.assignee_id = :playID
                    AND ae_ext_products_carts.cart_status = 'shopping'";

    $rows = \Yii::app()->db
    ->createCommand($sql)
    ->bindValues(array(
    ':playID' => $this->playid,
    )
    )
    ->queryAll();

    */



    /**
     * @return array|static[]
     */
    public function getParentCart() {

        $criteria = new \CDbCriteria();
        $criteria->condition = 'task.assignee_id = '.$this->playid ." AND cart_status = 'shopping'";
        $cart = ProductcartsModel::model()->with('task')->together()->findAll($criteria);

        if(!$cart){
            return array();
        }

        return $cart;
    }

    /**
     * @param null $playId
     * @param bool $taskid
     * @return mixed
     */
    public function getParentCartTotal($playId = null,$taskid=false){
        $cartData = $this->getParentCartData($playId,$taskid);
        return $cartData['cart_total'];
    }

    /**
     * @param null $playId
     * @param bool $taskid
     * @return mixed
     */
    public function getParentCartItems($playId = null,$taskid = false){
        $cartData = $this->getParentCartData($playId,$taskid);
        return $cartData['cart_items'];
    }

    /**
     * @param null $playId
     * @param bool $taskid
     * @return mixed
     */
    public function getParentCartData($playId = null,$taskid=false){

        $array['cart_total'] = 0;
        $array['cart_items'] = 0;

        $rows = $this->getParentCart();

        if(!$rows){
            return $array;
        }

        foreach($rows as $row){
            $array['cart_total'] = $array['cart_total'] + ($row->quantity) * $row->product->price;
            $array['cart_items'] = $array['cart_items'] + $row->quantity;
        }

        return $array;
    }

    /**
     * @return bool
     */
    public function confirmParentBuy() {

        $rows = $this->getParentCart();

        if (!$rows) {
            return false;
        }

        foreach ($rows as $row) {
            $row->cart_status = 'shipped';
            $row->update();
            if ($row->task_id) {
                $this->shipTask($row->task_id);
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function buyProduct($id,$cart) {


        foreach($cart as $cartitem){
            if($cartitem->product_id == $id){
                $obj = $cartitem;
            }
        }

        if(isset($cartitem)){
            $cartitem->cart_status = 'shipped';
            $cartitem->update();
            $test = ProductcartsModel::model()->findAllByAttributes(array('task_id' => $cartitem->task_id,'cart_status' => 'shopping'));
            if(!$test){
                $this->shipTask($cartitem->task_id);
                $cart = $this->getParentCartItems();
                if($cart == 0){
                    $this->deleteVariable('purchase_once');
                }
            }
        }


        return true;
    }

}
