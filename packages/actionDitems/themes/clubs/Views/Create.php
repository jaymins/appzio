<?php

namespace packages\actionDitems\themes\clubs\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\clubs\Components\Components as Components;
use packages\actionDitems\Models\Model as ArticleModel;

class Create extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentFormFieldText('',
            array(
                'hint' => '{#fan_club_name#}',
                'variable' => 'club_name'

            )
            );

        $this->layout->scroll[] = $this->uiKitButtonHollow('Save',
            array(

                'onclick' => [
                    $this->getOnclickSubmit('save'),
//                    $this->getOnclickGoHome()
                ]
            ));

        return $this->layout;
    }


}