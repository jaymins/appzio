<?php


namespace packages\actionDitems\themes\cityapp\Models;

use packages\actionDitems\Models\ItemModel;
use packages\actionDitems\Models\Model as BootstrapModel;

class Model extends BootstrapModel
{

    /**
     * Retrieve a single item from storage based on it's ID.
     * It's tags, category and owner are preloaded.
     *
     * @param $itemId
     * @return array|mixed|null|static
     */
    public function getItem(int $itemId): ItemModel
    {
        $item = ItemModel::model()->with('images_data')->findByPk($itemId);

        if (empty($item) || is_null($item)) {
            return new ItemModel();
        }

        $item->owner = \AeplayVariable::getArrayOfPlayvariables($item->play_id);
        
        return $item;
    }

    public function getSearchItems(string $searchterm)
    {

        $relations = [
            'images_data',
            'category_relations' => [
                'with' => [
                    'category' => [
                        'alias' => 'item_category',
                    ]
                ]
            ]
        ];

        $items = ItemModel::model()
                    ->with($relations)
                    ->findAll([
                        'condition' => 't.type = :type AND t.name LIKE :term',
                        'params' => [
                            ':type' => 'art_item',
                            ':term' => '%' . $searchterm . '%',
                        ]
                    ]);

        if ( empty($items) ) {
            return false;
        }

        return $items;
    }

}