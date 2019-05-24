<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMgdpr\Views;

use Bootstrap\Views\BootstrapView;


class View extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMgdpr\Components\Components
     */
    public $components;
    public $theme;

    public $tabs_data;
    public $home;
    public $delete;
    public $popup;
    public $header_text;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->tabs_data = $this->getData('tabs', 'array');
        $this->home = $this->getData('home', 'num');
        $this->delete = $this->getData('delete', 'bool');
        $this->popup = $this->getData('popup', 'bool');
        $this->header_text = $this->getData('headertext', 'string');
        $this->setHeader();

        if (isset($this->tabs_data['terms'])) {
            $this->layout->scroll[] = $this->getComponentText($this->tabs_data['terms'], [], [
                'font-size' => '14', 'margin' => '15 15 15 15', 'color' => $this->color_text_color,
                'font-ios' => 'Roboto'
            ]);
        }

        $this->setButtons();
        return $this->layout;
    }

    /* aka privacy */
    public function tab2()
    {
        $this->layout = new \stdClass();
        $this->setHeader(2);

        if (isset($this->tabs_data['privacy'])) {
            $this->layout->scroll[] = $this->getComponentText($this->tabs_data['privacy'], [], [
                'font-size' => '14', 'margin' => '15 15 15 15', 'color' => $this->color_text_color,
                'font-ios' => 'Roboto'
            ]);
        }

        $this->setButtons();
        return $this->layout;
    }

    /* aka subscriptions */
    public function tab3()
    {
        $this->layout = new \stdClass();
        $this->setHeader(3);

        if ($this->model->getSavedVariable('system_source') == 'client_iphone') {
            $content = isset($this->tabs_data['subscriptions_ios']) ? $this->tabs_data['subscriptions_ios'] : '';
        } else {
            $content = isset($this->tabs_data['subscriptions_android']) ? $this->tabs_data['subscriptions_android'] : '';
        }

        $this->layout->scroll[] = $this->getComponentText($content, [], [
            'font-size' => '14', 'margin' => '15 15 15 15', 'color' => $this->color_text_color,
            'font-ios' => 'Roboto'
        ]);

        $this->setButtons();
        return $this->layout;
    }


    public function setTabNavi($tab=1)
    {

        if (isset($this->tabs_data['terms']) AND count($this->tabs_data) > 1) {
            $tabs[] = array(
                'text' => strtoupper('{#terms#}'),
                'onclick' => $this->getOnclickTab(1),
                'active' =>  $tab == 1 ? true : false
            );
        }

        if (isset($this->tabs_data['privacy'])) {
            $tabs[] = array(
                'text' => strtoupper('{#privacy#}'),
                'onclick' => $this->getOnclickTab(2),
                'active' => $tab == 2 ? true : false
            );
        }

        if (isset($this->tabs_data['subscriptions_ios']) AND $this->model->getSavedVariable('system_source') == 'client_iphone') {
            $tabs[] = array(
                'text' => strtoupper('{#subscription#}'),
                'onclick' => $this->getOnclickTab(3),
                'active' => $tab == 3 ? true : false
            );
        }

        if (isset($this->tabs_data['subscriptions_android']) AND $this->model->getSavedVariable('system_source') == 'client_android') {
            $tabs[] = array(
                'text' => strtoupper('{#subscription#}'),
                'onclick' => $this->getOnclickTab(3),
                'active' => $tab == 3 ? true : false
            );
        }

        if(isset($tabs)){
            $this->layout->header[] = $this->uiKitTabNavigation($tabs, ['popup' => true]);
        }

    }

    public function setButtons()
    {
        $this->layout->footer[] = $this->getComponentSpacer('15');

        if ($this->popup) {
            $this->layout->footer[] = $this->uiKitButtonFilled('{#close#}', [
                'onclick' => $this->getOnclickClosePopup()
            ]);
        } elseif ($this->home) {
            $this->layout->footer[] = $this->uiKitButtonHollow('{#home#}', [
                'onclick' => $this->getOnclickOpenAction(false, $this->home)
            ]);
        }

        if ($this->delete) {
            $this->layout->footer[] = $this->getComponentSpacer('15');
            $this->layout->footer[] = $this->uiKitButtonFilled('{#delete_my_profile#}', [
                'onclick' => $this->getOnclickShowDiv('confirmation', $this->getClickParams())
            ]);
        }

        $this->layout->footer[] = $this->getComponentSpacer('15');
    }

    protected function getClickParams()
    {
        $clickparams['layout'] = new \stdClass();
        $clickparams['layout']->bottom = '80';
        $clickparams['layout']->left = '50';
        $clickparams['layout']->right = '50';
        $clickparams['transition'] = 'fade';
        $clickparams['tap_to_close'] = '1';
        return $clickparams;
    }

    public function setHeader($tab=1)
    {
        if ($this->hide_default_menubar) {
            if (!$this->model->getConfigParam('shown_in_popup')) {
                $params['icon_color'] = $this->color_top_bar_text_color == '#FFFFFFFF' ? 'white' : 'black';
                $params['mode'] = 'gohome';
                $params['title'] = $this->model->getConfigParam('subject');


                if (!empty($menu)) {
                    $params['right_menu'] = $menu;
                }

                $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
            }
        }

        if ($this->header_text) {
            $this->layout->header[] = $this->getComponentText($this->header_text, [], [
                'font-size' => '14', 'margin' => '15 15 0 15', 'color' => $this->color_text_color,'opacity' => '0.8'
            ]);
        }

        $this->setTabNavi($tab);
    }

    public function getDivs()
    {
        $divs = new \stdClass();

        /* look for traits under the components */
        $params['text'] = '{#are_you_sure_you_want_to_delete_your_user_account#}? {#this_action_is_irreversible#}.';
        $divs->confirmation = $this->components->uiKitRemoveUserDiv($params);
        return $divs;
    }


}
