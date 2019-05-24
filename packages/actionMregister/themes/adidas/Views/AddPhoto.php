<?php

namespace packages\actionMregister\themes\adidas\Views;
use packages\actionMregister\Views\Pagetwo as BootstrapView;
use packages\actionMregister\themes\adidas\Components\Components;

class AddPhoto extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;
    public $tab;

    public function tab1()
    {
        $this->layout = new \stdClass();

//        $this->model->rewriteActionConfigField('bckgr_img_portrait', '');

        $this->model->rewriteActionConfigField('hide_menubar', '1');

        $this->layout->scroll[] = $this->components->getPhotoField('mreg_collect_photo');
        if (isset($this->model->validation_errors['profilepic']) && !empty($this->model->validation_errors['profilepic'])) {
            $this->layout->scroll[] = $this->getComponentText($this->model->validation_errors['profilepic'], array(), array(
                'color' => '#FFFFFF',
                'font-size' => 24,
                'text-align' => 'center',
                'margin' => '0 0 10 0'
            ));
        }


        $this->layout->footer[] = $this->uiKitButtonHollow('{#continue#}',
            array(
                'onclick' => $this->getOnclickSubmit('continue')),
            array(
                'margin' => '20 80 20 80',
                'color' => '#ffffff',
                'border-color' => '#ffffff'
            )
        );

        return $this->layout;
    }

}