<?php

namespace packages\actionDitems\themes\cityapp\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\cityapp\Components\Components as Components;

class Searchlist extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;


    public function tab1()
    {
        $this->layout = new \stdClass();

        $searchterm = $this->getData('searchterm', 'mixed');
        $search_items = $this->getData('search_items', 'mixed');

        if ( empty($searchterm) OR empty($search_items) ) {
            // To do: Render a message
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

        $this->layout->scroll[] = $this->getComponentSpacer(20);

        $this->layout->scroll[] = $this->getComponentDivider([], [
            'parent_style' => 'article-uikit-divider-thin',
            'margin' => '0 0 10 0',
        ]);

        foreach ($search_items as $item) {

            $image = $this->getFeaturedImage($item->images_data);
            $category = $item->category_relations[0]->category->name;

            $this->layout->scroll[] = $this->components->getSearchItem($image, $item->name, $category, array(
                'onclick' => $this->getOnclickOpenAction('viewart', false, [
                    'id' => $item->id,
                    'sync_open' => 1,
                    'back_button' => 1
                ]),
                'divider' => true
            ));
        }

        return $this->layout;
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