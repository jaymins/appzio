<?php
/**
 * Created by PhpStorm.
 * User: trailo
 * Date: 2.10.18
 * Time: 9:32
 */

namespace packages\actionMfood\Components;


trait SelectListCategory
{

    /**
     * This will output a single category which belongs to a shopping list, such as
     * "Fridge"
     * @param $list
     * @return mixed
     */

    public function getSelectListCategory($list)
    {
        $listItems = [];
        foreach ($list as $id => $item) {
            $listItems[] =
                $this->getComponentRow([
                    $this->getComponentText($item, [], [
                        'font-size' => 16,
                        'color' => $this->color_text_color,
                        'width' => '100%',
                        'text-alignment' => 'left',
                        'padding' => '10 20 10 20',
                    ]),
                    $this->getComponentText(' ', [], [
                        'height' => '1',
                        'background-color' => '#383d44'
                    ])
                ], [
                    'onclick' => [
                        //$this->getOnclickSetVariables([$this->model->getVariableId('ingredient_category') => $id]),
                        $this->getOnclickHideDiv('ingredient_category')
                        ],
                    'variable' => 'ingredient_category'
                ], [
                    'width' => '100%',
                    'vertical-alignment' => 'middle',
                ]);
        }
       return $this->getComponentColumn($listItems,[],[]);
    }
}