<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMmenus\themes\adidas\Controllers;

use packages\actionMmenus\themes\adidas\Views\Main as ArticleView;
use packages\actionMmenus\themes\adidas\Models\Model as ArticleModel;

class Main extends \packages\actionMmenus\Controllers\Main {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public $items;
    public $role;
    public $data = [];

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault(){

        $this->role = $this->model->getSavedVariable('role');

        if ( $this->model->getSavedVariable('logged_in') ) {
            $this->data['items'] = $this->getMenuItems();
            $this->setProfileData();
        } else {
            $this->data['items'] = $this->getLoggedOutItems();
        }

        return ['Main', $this->data];
    }

    public function getLoggedOutItems() {
        return array(
            array(
                'name' => '{#login#}',
                'link' => 'login',
            ),
            array(
                'name' => '{#register#}',
                'link' => 'register',
            ),
            array(
                'name' => '{#terms_and_conditions#}',
                'link' => 'terms',
            ),
            array(
                'name' => '{#privacy#}',
                'link' => 'privacypolicy',
            ),
        );
    }

    public function getMenuItems() {

        switch ($this->role) {
            case 'fan':
                $safeName = 'side_menu_fan';
                break;
            case 'venue':
                $safeName = 'side_menu_venue';
                break;
            default:
                $safeName = 'side_menu';

        }
        $this->items = \Aenavigation::model()
            ->with('menu_items')
            ->findByAttributes(
                array(
                    'app_id' => $this->model->appid,
                    'safe_name' => $safeName,
                )
            );

        if ( !empty($this->items->menu_items) ) {
            return $this->prepareItems();
        }
        
        return [
            [
                'name' => '{#home#}',
                'link' => 'home',
                'icon' => 'tatjack-icon-home.png',
            ],
        ];
    }

    public function prepareItems() {

        $items = [];

        foreach ($this->items->menu_items as $menu_item) {

            $link = $menu_item->action_config;

            if ( $menu_item->action == 'go-home' ) {
                $link = 'go-home';
            }

            $items[] = [
                'name' => $menu_item->name,
                'link' => $link,
                'icon' => $menu_item->icon,
            ];
        }

        return $items;
    }

    private function setProfileData() {

        $name = '{#anonymous#}';
        $profile_path = 'login';

        if ($this->model->getSavedVariable('firstname') AND $this->model->getSavedVariable('lastname')) {
            $name = $this->model->getSavedVariable('firstname') . ' ' . $this->model->getSavedVariable('lastname');
        }

        if ($this->model->getSavedVariable('reg_phase') AND $this->model->getSavedVariable('reg_phase') == 'complete') {
            $profile_path = 'myprofile';
        }

        $this->data['show_profile'] = true;
        $this->data['name'] = $name;
        $this->data['profilepic'] = $this->model->getSavedVariable('profilepic', 'tatjack-profile-placeholder.jpg');
        $this->data['profile_path'] = $profile_path;

    }

}