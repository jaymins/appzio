<?php
/**
 * Created by PhpStorm.
 * User: trailo
 * Date: 2.10.18
 * Time: 9:32
 */

namespace packages\actionMfood\Components;


trait ShoppingListCategory
{

    /**
     * This will output a single category which belongs to a shopping list, such as
     * "Fridge"
     * @param array $items
     * @return mixed
     */

    public function getShoppingListCategory( $list)
    {
        $listItems = [];
        foreach ($list as $item){
          $listItems[] =
              $this->getComponentRow([
                  $this->getComponentText($item['text'], [], [
                      'font-size' => 14,
                      'color' => $this->color_text_color,
                      'width' => '100%',
                      'text-alignment' => 'left',
                      'padding' => '10 20 10 20',
                  ])
              ],[
                  'onclick' => $this->getOnclickSubmit('shoppinglist/complete/'.$item['record_id'])
              ],[
                  'width' => '100%',
                  'background-image' => $this->getCheckboxImage($item['is_active']),
                  'background-position' => '95% 50%',

                  'vertical-alignment' => 'middle',
              ]);
        }
        return $listItems;
    }

    private function getCheckboxImage($state)
    {
        $checked_image = $this->getImageFileName('theme-icon-selector-check.png', [
            'imgwidth' => '80',
            'imgheight' => '80',
            'priority' => '1'
        ]);

        $unchecked_image = $this->getImageFileName('theme-icon-selector-round.png', [
            'imgwidth' => '80',
            'imgheight' => '80',
            'priority' => '1'
        ]);
        $bg_image = ($state == 1) ? $checked_image : $unchecked_image;

        return $bg_image;
    }

}