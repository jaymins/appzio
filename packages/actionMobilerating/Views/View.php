<?php

namespace packages\actionMobilerating\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMobilerating\Controllers\Components;
use function stristr;
use AeplayVariable;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView
{

    /* @var \packages\actionMobilerating\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getHeader();

        $this->layout->scroll[] = $this->getComponentSpacer('50');

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

    public function getHeader()
    {
        $toggleSidemenu = new \stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $this->layout->header[] = $this->getComponentRow(array(
            $this->getComponentImage('ic_menu_new.png', array(
                'onclick' => $toggleSidemenu
            ), array(
                'width' => '20',
            )),
            $this->getComponentText('{#rate_this_app#}', array(), array(
                'color' => '#ff6600',
                'width' => '90%',
                'text-align' => 'center'
            ))
        ), array(), array(
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 3,
            'shadow-offset' => '0 1',
            'padding' => '10 20 10 20',
            'width' => '100%',
        ));

        $this->layout->header[] = $this->getComponentImage('header-shadow-white.png', array(
            'imgwidth' => '1440',
            'width' => '100%',
        ));
    }

    public function renderStars()
    {
        $rows = array();

        for ($i = 0; $i <= 5; $i++) {
            $stars = $this->renderStarIterations($i);

            $this->layout->scroll[] = $this->getComponentRow($stars, array(
                'id' => $i,
                'visibility' => $i > 0 ? 'hidden' : ''
            ), array(
                'text-align' => 'center'
            ));
        }
    }

    public function renderStarIterations($number)
    {
        $stars = array();

        for ($i = 1; $i <= 5; $i++) {
            $actions = array(
                $this->getOnclickHideElement('0', array('transition' => 'none')),
                $this->getOnclickHideElement('1', array('transition' => 'none')),
                $this->getOnclickHideElement('2', array('transition' => 'none')),
                $this->getOnclickHideElement('3', array('transition' => 'none')),
                $this->getOnclickHideElement('4', array('transition' => 'none')),
                $this->getOnclickHideElement('5', array('transition' => 'none')),
                $this->getOnclickShowElement($i, array('transition' => 'none'))
            );

            if ($i < 4) {
                $actions[] = $this->getOnclickShowElement('feedback-form');
                $actions[] = $this->getOnclickShowElement('feedback-button', array('transition' => 'none'));
                $actions[] = $this->getOnclickHideElement('rating-button', array('transition' => 'none'));

            } else {
                $actions[] = $this->getOnclickHideElement('feedback-form');
                $actions[] = $this->getOnclickHideElement('feedback-button', array('transition' => 'none'));
                $actions[] = $this->getOnclickShowElement('rating-button', array('transition' => 'none'));
            }

            $stars[] = $this->getComponentImage(($i > $number ? 'desee-star.png' : 'desee-star-full.png'), array(
                'onclick' => $actions
            ), array(
                'width' => '50',
                'margin' => '0 5 0 5'
            ));
        }

        return $stars;
    }

    public function renderFeedbackForm()
    {
        $this->layout->scroll[] = $this->getComponentColumn(array(
            $this->getComponentFormFieldTextArea('', array(
                'hint' => '{#please_describe_how_to_make_the_app_better#}',
                'variable' => 'feedback'
            ), array(
                'color' => '#8e8e8e',
                'border-width' => '1',
                'border-color' => '#e0e0e0',
                'border-radius' => '5',
                'padding' => '10 10 10 10',
                'margin' => '30 20 0 20',
            ))
        ), array(
            'id' => 'feedback-form',
            'visibility' => 'hidden'
        ), array());
    }

    public function renderButton()
    {
        $this->layout->scroll[] = $this->getComponentText('{#submit_feedback#}', array(
            'onclick' => $this->getOnclickSubmit('feedback'),
            'id' => 'feedback-button',
            'visibility' => 'hidden',
        ), array(
            'width' => 'auto',
            'margin' => '25 20 10 20',
            'background-color' => '#fec02e',
            'color' => '#1d0701',
            'padding' => '13 5 13 5',
            'text-align' => 'center',
            'border-radius' => '8',
        ));

        $this->layout->scroll[] = $this->getComponentText('{#submit_feedback#}', array(
            'onclick' => $this->getRatingOnclick(),
            'id' => 'rating-button'
        ), array(
            'width' => 'auto',
            'margin' => '25 20 10 20',
            'background-color' => '#fec02e',
            'color' => '#1d0701',
            'padding' => '13 5 13 5',
            'text-align' => 'center',
            'border-radius' => '8',
        ));
    }

    public function getRatingOnclick()
    {
        $vars = AeplayVariable::getArrayOfPlayvariables($this->model->playid);
        $source = isset($vars['system_source']) ? $vars['system_source'] : false;

        if ($source == 'client_android') {
            $url = $this->model->getConfigParam('rate_url_android');
        } else {
            $url = $this->model->getConfigParam('rate_url_ios');
        }

        $onclick = new \StdClass();
        $onclick->action = 'open-url';
        $onclick->id = 'open-url';
        $onclick->action_config = $url;

        return $onclick;
    }

}