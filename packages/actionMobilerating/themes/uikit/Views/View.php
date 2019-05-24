<?php

namespace packages\actionMobilerating\themes\uikit\Views;
use packages\actionMobilerating\Controllers\Controller;
use packages\actionMobilerating\Views\View as BootstrapView;
use packages\actionMobilerating\themes\uikit\Components\Components;

class View extends \packages\actionMobilerating\Views\View {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->getComponentSpacer('50');

        if($this->hide_default_menubar){
            /* top bar if logo is set */
            $params['mode'] = 'sidemenu';
            $params['hairline'] = '#e5e5e5';
            $params['icon_color'] = 'white';

            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }

            $params['title'] = $this->model->getConfigParam('subject') ? $this->model->getConfigParam('subject') : '{#send_feedback#}';
            $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
        }


        $image = $this->model->getConfigParam('actionimage1') ? $this->model->getConfigParam('actionimage1') : 'desee-star-big.png';

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentImage($image, array(), array(
                'width' => '35%',
            )),
        ), array(), array(
            'margin' => '0 0 25 0',
            'text-align' => 'center',
        ));

        $this->layout->scroll[] = $this->getComponentText('{#show_some_love#}', array(), array(
            'color' => '#ff6600',
            'font-size' => '25',
            'text-align' => 'center',
            'margin' => '0 0 25 0',
        ));

        if ($this->getData('message', 'string')) {
            $this->layout->scroll[] = $this->getComponentText($this->getData('message', 'string'), array(), array(
                'color' => '#8e8e8e',
                'text-align' => 'center',
                'border-width' => '1',
                'border-color' => '#e0e0e0',
                'border-radius' => '5',
                'padding' => '5 5 5 5',
                'margin' => '0 20 0 20',
            ));
            return $this->layout;
        }

        $this->renderStars();
        $this->renderFeedbackForm();
        $this->renderButton();

        return $this->layout;
    }

}