<?php


namespace packages\actionDitems\themes\fanshop\Models;
use packages\actionDitems\Models\ItemModel;
use packages\actionDitems\Models\Model as BootstrapModel;

class Model extends BootstrapModel {


    public function setBackgroundColor($color = '#ffffff')
    {
        $this->rewriteActionConfigField('background_color', '#F0F3F8');
    }

    /**
     * Validate submitted variables.
     * Errors are filled in validation_errors property which is used by the controller.
     *
     * @return void
     */


    public function saveItem($images, $tags, $type = '')
    {
        $item = ItemModel::store(array_merge($this->getAllSubmittedVariablesByName(), [
            'play_id' => $this->playid,
            'game_id' => $this->appid,
            'type' => $type,
            'images' => $images,
            'status' => 'active',
            'date_added' => time(),
            'lat' => $this->getSavedVariable('lat'),
            'lon' => $this->getSavedVariable('lon'),
            'category_id' => null
        ]));

        $tagIds = $this->saveAndGetTagIds($tags);
        $categoryIds = $this->getSubmittedCategoryIds();

        $this->saveItemAndTagRelation($item->id, $tagIds);
        $item->owner = \AeplayVariable::getArrayOfPlayvariables($item->play_id);

        return $item;
    }

    public function validateInput()
    {
        $requiredVariables = array(
            'name',
            'time',
            'description'
        );

        $submittedVariables = $this->getAllSubmittedVariablesByName();

        foreach ($submittedVariables as $key => $value) {
            if (!in_array($key, $requiredVariables)) {
                continue;
            }

            if (empty($value)) {
                $this->validation_errors[$key] = 'The ' . $key . ' field is required';
            }
        }

        $images = $this->getItemImages();

        if (empty($images)) {
            $this->validation_errors['images'] = 'Add at least one image';
        }

        if (empty($this->getSubmittedVariableByName('price'))) {
            $this->submitvariables['price'] = 'Per Request';
        }
    }

    public function handleBoostPurchase(){
        if(isset($_REQUEST['purchase_completed']) AND $_REQUEST['purchase_completed'] == 1 AND isset($_REQUEST['purchase_product_id'])){
            if($_REQUEST['purchase_product_id'] == 'product_boost.01'){
                $obj = ItemModel::model()->findByPk($this->getItemId());
                if($obj){
                    $obj->featured = 1;
                    $obj->update();
                }
            }

        }
    }



}
