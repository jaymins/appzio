<?php

namespace backend\modules\items\models;

use backend\modules\items\models\base\AeExtItem as BaseAeExtItem;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_items".
 */
class AeExtItem extends BaseAeExtItem
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['date_added'], 'default', 'value' => time()],
                [['status'], 'default', 'value' => '1'],
                [['featured'], 'default', 'value' => '0'],
                [['external'], 'default', 'value' => '0'],
                [['slug'], 'default', 'value' => uniqid()],
            ]
        );
    }

    public static function addUploadedImages(AeExtItemsImage $item_images, int $item_id)
    {
        if (
            !isset($_FILES['images-group']['name'][0]['images']) OR
            empty($_FILES['images-group']['name'][0]['images'])
        ) {
            return false;
        }

        $uploads = $_FILES['images-group'];
        $item_images->addImages($item_id, $uploads);

        return true;
    }

    public static function addOrUpdateUploadedImages(AeExtItemsImage $item_images, AeExtItem $item_model)
    {
        $uploads = $_FILES['images-group'];
        $repeater_data = Yii::$app->request->post('images-group');

        $item_images->updateImages($item_model, $uploads, $repeater_data);

        return true;
    }

}