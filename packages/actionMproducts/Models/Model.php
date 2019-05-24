<?php


namespace packages\actionMproducts\Models;
use Bootstrap\Models\BootstrapModel;
use packages\actionMearnster\Models\ConfigureMenu;
use packages\actionMtasks\Models\Tasks;
use function str_replace_array;

class Model extends BootstrapModel {

    public $validation_errors;
    public $category_id;
    public $product_id;
    private $cartdata;

    use Cart;
    use Tasks;
    use ConfigureMenu;

    /* gets a list of configuration fields, these are used by the view */
    public function getFieldList(){
        $params = $this->getAllConfigParams();
        $output = array();

        foreach($params as $key=>$item){
            if(stristr($key, 'mreg') AND $item){
                $output[] = $key;
            }
        }

        return $output;
    }

    /**
     * @return array|mixed|null|static[]
     */
    public function getCategories(){
        $categories = ProductcategoriesModel::model()->findAllByAttributes(array('app_id' => $this->appid),array('order' => 'sorting'));

        if($categories){
            return $categories;
        }

        return array();
    }

    /**
     * @return array|mixed|null|static[]
     */
    public function getFeaturedProducts(){
        $categories = ProductitemsModel::model()->findAllByAttributes(array('app_id' => $this->appid,'featured' => 1),array('order' => 'id DESC'));

        if($categories){
            return $categories;
        }

        return array();
    }

    /**
     * @return array|mixed|null|static
     */
    public function getCategoryInfo(){
        $id = $this->getCategoryId();

        if($id){
            return ProductcategoriesModel::model()->findByPk($id);
        }
    }

    /**
     * @return bool|mixed
     */
    public function setCategoryId(){
        $this->category_id = $this->getItemId();
        return $this->category_id;
    }

    /**
     * @return bool|mixed
     */
    public function getCategoryId(){
        if($this->category_id){
            return $this->category_id;
        }

        return $this->setCategoryId();
    }

    /**
     * @return array
     */
    public function searchProducts(){
        $term = $this->getSubmittedVariableByName('searchterm');

        $sql = "SELECT * FROM ae_ext_products WHERE
                (`title` LIKE '%$term%' OR `header` LIKE '%$term%' OR `description` LIKE '%$term%')
                AND app_id = :appid
                ORDER BY `id` DESC 
        ";


        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                    ':appid' => $this->appid,
                )
            )
            ->queryAll();

        if($rows){
            foreach($rows as $row){
                $output[] = (object) $row;
            }

            return $output;
        }

        return array();

    }

    /**
     * @param string $sorting
     * @return array|mixed|null|static[]
     */
    public function getProducts($sorting='id'){
        switch($sorting){
            case 'id':
                $sort = 'id';
                break;

            default:
                $sort = 'id';
                break;
        }

        return ProductitemsModel::model()->findAllByAttributes(array('category_id' => $this->category_id),array('order' => $sort));
    }


    /**
     * @return array|mixed|null|static
     */
    public function getProductInfo($id=false){
        if(!$id){
            $id = $this->getProductId();
        }

        if($id){
            return ProductitemsModel::model()->with('photos')->findByPk($id);
        }
    }

    /**
     * @return bool|mixed
     */
    public function setProductId(){
        $this->product_id = $this->getItemId();
        return $this->product_id;
    }

    /**
     * @return bool|mixed
     */
    public function getProductId(){
        if($this->product_id){
            return $this->product_id;
        }

        return $this->setProductId();
    }
}
