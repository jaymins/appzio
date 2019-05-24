<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMmenus\themes\cityapp\Controllers;

use packages\actionMmenus\themes\cityapp\Models\Model as ArticleModel;
use packages\actionMmenus\themes\cityapp\Views\Main as ArticleView;

class Main extends \packages\actionMmenus\Controllers\Main
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public $items;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        $data = [];

        if ($this->model->getSavedVariable('logged_in')) {
            $data['items'] = $this->getMenuItems();
        } else {
            $data['items'] = $this->getLoggedOutItems();
        }

        $data['user_logged_in'] = (bool)$this->model->getSavedVariable('logged_in');
        $data['logout_image'] = $this->model->getConfigParam('actionimage1');

        return ['Main', $data];
    }

    public function getLoggedOutItems()
    {
        return array(
            array(
                'name' => 'Login',
                'link' => 'login',
            ),
            array(
                'name' => 'Register',
                'link' => 'register',
            ),
        );
    }

    public function getMenuItems()
    {

        $this->items = \Aenavigation::model()
            ->with('menu_items')
            ->findByAttributes(
                array(
                    'app_id' => $this->model->appid,
                    'safe_name' => 'side_menu',
                )
            );

        if (!empty($this->items->menu_items)) {
            return $this->prepareItems();
        }

        return [
            [
                'name' => '{#home#}',
                'link' => 'home',
                'icon' => 'electric-icon-home.png',
            ],
            [
                'name' => '{#settings#}',
                'link' => 'settings',
                'icon' => 'electric-icon-settings.png',
            ],
            [
                'name' => '{#appliances#}',
                'link' => 'alldevices',
                'icon' => 'electric-icon-appliances.png',
            ],
            [
                'name' => '{#notifications#}',
                'link' => 'notifications',
                'icon' => 'electric-icon-notifications.png',
            ],
            [
                'name' => '{#tips#}',
                'link' => 'tips',
                'icon' => 'electric-icon-tips.png',
            ],
            [
                'name' => '{#feedback#}',
                'link' => 'feedback',
                'icon' => 'edit.png',
            ],
        ];
    }

    public function prepareItems()
    {

        $items = [];

        foreach ($this->items->menu_items as $menu_item) {
            $items[] = [
                'name' => $menu_item->name,
                'link' => $menu_item->action_config,
                'icon' => $menu_item->icon,
            ];
        }

        return $items;
    }

}