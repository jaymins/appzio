<?php

namespace packages\actionMfitness\Components;

use Bootstrap\Components\BootstrapComponent;
use packages\actionMfitness\Models\ProgramCategoriesModel;

trait FitnessCategory
{

    public function getFitnessCategory(ProgramCategoriesModel $category, array $parameters = array(), array $styles = array())
    {
        return $this->getComponentColumn([
            $this->getComponentColumn([
                $this->getComponentImage($category->icon, ['priority' => '1'], [
                    'height' => ($category->name != 'Training')?'50%':'40%'
                ]),
            ], [], [
                'text-align' => 'center',
                'vertical-align' => 'middle',
                'height' => $this->screen_height / 8,
            ]),
            $this->getComponentText($category->name, [], [
                'font-size' => '17',
                'color' => '#ffffff',
                'text-align' => 'center',
                'padding' => '5 15 25 15',
            ]),
            $this->getComponentSpacer(2, [], [
                'width' => 'auto',
                'background-color' => $category->color
            ])
        ], [
            'onclick' => $this->getOnclickOpenAction($parameters['action'], false, [
                'id' => 'fitness-category-' . $category->id,
                'sync_open' => 1,
                'back_button' => 1,
            ]),
        ], [
            'width' => '50%',
            'margin' => '0 2 0 2',
            'background-color' => '#1e1e1e',
        ]);
    }

}