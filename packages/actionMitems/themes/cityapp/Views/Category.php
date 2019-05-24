<?php

namespace packages\actionMitems\themes\cityapp\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\cityapp\Components\Components as Components;

class Category extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;

    private $cat_data;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->cat_data = $this->getData('category_data', 'mixed');

        if ( empty($this->cat_data) ) {
            // To do: Render a message
        }

        $this->layout->header[] = $this->getComponentText($this->cat_data->name, [], [
            'font-size' => 24,
            'font-weight' => 'bold',
            'color' => '#ef4345',
            'padding' => '20 15 20 15',
        ]);

        $this->getCategoryItems();

        return $this->layout;
    }

    private function getCategoryItems()
    {
        
        if ( empty($this->cat_data->category_relations) ) {
            return false;
        }

        $rows = array_chunk($this->cat_data->category_relations, 2);

        foreach ($rows as $i => $row) {

            $items = [];

            foreach ($row as $j => $item) {
                
                $item = $item->item;

                $featured_image = $this->getFeaturedImage( $item->images_data );

                $items[] = $this->components->getImageCard($item->name, $featured_image, [
                    'onclick' => $this->getOnclickOpenAction('viewart', false, [
                        'id' => $item->id,
                        'back_button' => 1
                    ])
                ], [
                    'width' => '50%',
                    'margin' => ( !$j ? '0 7 0 0' : '0 0 0 7' ),
                ]);
            }

            $this->layout->scroll[] = $this->getComponentRow($items, [], [
                'margin' => ( !$i ? '15 15 15 15' : '0 15 15 15' )
            ]);
        }

        return true;
    }

    private function getFeaturedImage($images_data)
    {

        if ( empty($images_data) ) {
            // To do: return a placeholder
            return 'cityapp-placeholder.png';
        }

        foreach ($images_data as $image) {
            if ( $image->featured ) {
                return $image->image;
            }
        }

        return 'cityapp-placeholder.png';
    }

}