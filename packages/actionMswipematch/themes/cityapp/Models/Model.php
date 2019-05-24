<?php

namespace packages\actionMswipematch\themes\cityapp\Models;

use packages\actionMitems\Models\ItemModel;
use packages\actionMswipematch\Models\Model as BootstrapModel;

class Model extends BootstrapModel
{

    public function getItemsByAuthor(int $profile_id)
    {

        $items = ItemModel::model()->with('images_data')->findAllByAttributes([
            'play_id' => $profile_id
        ]);

        if ( empty($items) ) {
            return false;
        }
        
        return $items;
    }

}