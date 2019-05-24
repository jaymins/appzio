<?php

class FavouriteitemsMobileclassifiedsModel extends ArticleModel {

    public $id;
    public $play_id;
    public $item_id;
    public $favourite;

    public function tableName(){
        return 'ae_ext_classifieds_favourite_items';
    }

    public function toggleFavouriteItem($itemid) {

        $model = new FavouriteitemsMobileclassifiedsModel();
        $modelExists = $model->findByAttributes(['play_id' => $this->factory->playid, 'item_id' => $itemid]);

        if ($modelExists) {
            $model = $modelExists;
            $model->favourite = !$model->favourite;
            $model->update();
        } else {
            $model->play_id = $this->factory->playid;
            $model->item_id = $itemid;
            $model->favourite = 1;
            $model->insert();
        }

    }

}