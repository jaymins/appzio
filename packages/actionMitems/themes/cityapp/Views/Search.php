<?php

namespace packages\actionMitems\themes\cityapp\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\cityapp\Components\Components as Components;

class Search extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;


    public function tab1()
    {
        $this->layout = new \stdClass();

        $category_list = $this->getData('category_list', 'mixed');

        if ( empty($category_list) ) {
            $this->layout->scroll[] = $this->getComponentText('{#no_categories_yet#}');
            return $this->layout;
        }

        $this->layout->header[] = $this->getComponentText('Търси изкуство', [], [
            'font-size' => 24,
            'font-weight' => 'bold',
            'color' => '#ef4345',
            'padding' => '20 15 20 15',
        ]);

        $this->layout->header[] = $this->components->getSearchBar([
            'hint' => 'Търси изкуство',
            'onclick_close' => $this->getOnclickSubmit('Search/default/cancelsearch`'),
            'onclick_submit' => 'Search/default/search'
        ]);

        foreach ($category_list as $category) {
            
            $related_data = $category->category_relations;

            $images = [];

            foreach ($related_data as $related_item) {
                $db_images = $related_item->item->images_data;

                if ( empty($db_images) )
                    continue;

                foreach ($db_images as $image) {
                    if ( $image->featured ) {
                        $images[] = $this->components->getImageCard($related_item->item->name, $image->image, [
                            'onclick' => $this->getOnclickOpenAction('viewart', false, [
                                'id' => $related_item->item_id,
                                'sync_open' => 1,
                                'back_button' => 1
                            ])
                        ]);
                    }
                }
            }

            $this->layout->scroll[] = $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentText($category->name, [], [
                        'font-size' => '18',
                        'padding' => '0 15 0 15',
                    ]),
                    $this->getComponentColumn([
                        $this->getComponentRow([
                            $this->getComponentText('placeholder', [], [
                                'font-size' => 0
                            ])
                        ], [], [
                            'width' => '100%',
                            'height' => '2',
                            'vertical-align' => 'middle',
                            'background-color' => '#f5f5f5',
                        ])
                    ], [], [
                        'width' => 'auto',
                    ])
                ], [
                    'onclick' => $this->getOnclickOpenAction('category', false, [
                        'id' => $category->id,
                        'sync_open' => 1,
                        'back_button' => 1
                    ])
                ], [
                    'padding' => '15 0 15 0',
                    'vertical-align' => 'middle',
                ]),
                $this->getComponentRow($images, [
                    'scrollable' => 1,
                    'animate' => 'nudge',
                    'hide_scrollbar' => 1,
                ])
            ], [], [
                'margin' => '0 0 30 0'
            ]);

        }

        return $this->layout;
    }

}