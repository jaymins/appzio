<?php

namespace packages\actionMitems\Models;

use CActiveRecord;

class ItemImagesModel extends CActiveRecord
{
    public $id;
    public $item_id;
    public $date;
    public $image;
    public $featured;
    public $image_order;

    public function tableName()
    {
        return 'ae_ext_items_images';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'item' => array(self::BELONGS_TO, 'packages\actionMitems\Models\ItemModel', 'item_id')
        );
    }

    public static function storeImages(array $images, $item_id)
    {
        if (empty($images)) {
            return false;
        }

        $data = [];

        foreach ($images as $image) {
            $data[] = [
                'item_id' => $item_id,
                'date' => time(),
                'image' => $image,
                'featured' => 0,
                'image_order' => 0,
            ];
        }

        $builder = \Yii::app()->db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand('ae_ext_items_images', $data);
        $command->execute();

        return true;
    }

}