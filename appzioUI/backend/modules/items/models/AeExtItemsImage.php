<?php

namespace backend\modules\items\models;

use backend\components\Helper;
use backend\modules\items\models\base\AeExtItemsImage as BaseAeExtItemsImage;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_items_images".
 */
class AeExtItemsImage extends BaseAeExtItemsImage
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
                [['date'], 'default', 'value' => time()],
                [['featured'], 'default', 'value' => '0'],
            ]
        );
    }

    public function addImages(int $item_id, array $uploads)
    {

        $destination = Helper::getUploadPath();

        $upload_data = $this->getTmpImages($uploads);

        if (empty($upload_data)) {
            return false;
        }

        // Copy the files and store them to the DB
        foreach ($upload_data as $j => $file) {
            copy($file['tmp_name'], $destination . $file['name']);

            $model = new AeExtItemsImage;
            $model->item_id = $item_id;
            $model->date = time();
            $model->image = $file['name'];
            $model->image_order = $j;
            $model->save();
        }

        return true;
    }

    public function updateImages(AeExtItem $item_model, array $uploads, array $repeater_data)
    {

        $destination = Helper::getUploadPath();

        $new_uploads = $this->getTmpImages($uploads);
        $db_images = $item_model->aeExtItemsImages;

        // No new uploads - check of deleted items / or items with changed order
        if (empty($new_uploads)) {
            return false;
        }

        foreach ($new_uploads as $i => $file) {

            // No changes
            if (!isset($file['name']) OR empty($file['name'])) {
                continue;
            }

            copy($file['tmp_name'], $destination . $file['name']);

            $model = new AeExtItemsImage;

            if (isset($db_images[$i])) {

                $db_data = $repeater_data[$i];
                $db_image_id = $db_data['field-image-id'];

                $image_item = $model->findOne($db_image_id);

                // Something went wrong
                if (empty($image_item))
                    continue;

                $image_item->image = $file['name'];
                $image_item->date = time();
                $image_item->update();

            } else {
                $model->item_id = $item_model->id;
                $model->date = time();
                $model->image = $file['name'];
                $model->image_order = $i;
                $model->save();
            }

        }

        return true;
    }

    private function getTmpImages(array $uploads)
    {
        $result = [];

        foreach ($uploads['name'] as $i => $upload) {
            if (empty($upload['images']))
                continue;

            $result[$i]['name'] = $upload['images'];
        }

        if (empty($result)) {
            return [];
        }

        foreach ($uploads as $key => $items) {

            if ($key === 'name')
                continue;

            foreach ($items as $j => $item) {
                $result[$j][$key] = $item['images'];
            }
        }

        return $result;
    }

}