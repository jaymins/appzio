<?php
/**
 * Created by PhpStorm.
 * User: trailo
 * Date: 2.10.18
 * Time: 9:30
 */

namespace packages\actionMfood\Components;


trait DivAddShoppingListItem
{
    public function getDivAddShoppingListItem()
    {
        $width = $this->screen_width / 6;
        $div[] = $this->getComponentRow([
            $this->uiKitTopbarWithButtons([
                'leftSection' => [
                    'image' => 'cross.png',
                    'onclick' => $this->getOnclickHideDiv('add_my_own_item')
                ],
                'centerSection' => [
                    'title' => strtoupper('{#add_custom_unit#}'),
                ],
                'rightSection' => [],
            ])
        ], [], [
            'margin' => '0 0 25 0',
            'font-weigh' => 'bold'
        ]);
        return $this->getComponentColumn($div, [], [
            'text-align' => 'center',
            'height' => '100%',
            'background-color' => $this->color_topbar_hilite
        ]);
    }
}