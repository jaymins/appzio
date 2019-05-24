<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.article.controllers.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class deseeMainMobilemenus extends deseeMobilemenusSubController {

    public $items = array();
    public $metas;

    public function tab1(){
        $this->data = new stdClass();

        $this->rewriteActionConfigField( 'background_color', '#FAFAFA');

        $this->getHeading();
        $this->getNavItems();

        $background = $this->getImageFileName('menu-background.png');

        $this->data->scroll[] = $this->getColumn($this->items, array(
            'height' => $this->screen_height - 50,
            // 'background-image' => $background,
            // 'background-size' => 'cover',
            // 'style' => 'property-menu-shadowbox',
        ));

        return $this->data;
    }

    public function getHeading() {

        $name = '{#anonymous#}';
        $profilepic = $this->getVariable('profilepic') ? $this->getVariable('profilepic') : 'photo-placeholder.jpg';

        if ( $this->getSavedVariable( 'name' ) ) {
            $name = $this->getSavedVariable( 'name' );
        }

        if ( $this->getSavedVariable( 'real_name' ) ) {
            $name = $this->getSavedVariable( 'real_name' );
        }

        $profile_path = 'login';
        if ( $this->getVariable( 'reg_phase' ) AND $this->getVariable( 'reg_phase' ) == 'complete' ) {
            $profile_path = 'myprofile';
        }

        $onclick = new stdClass();
        $onclick->id = 'open-profile';
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getActionidByPermaname( $profile_path );
        $onclick->config = $fig = $this->getActionidByPermaname( $profile_path );
        //$onclick->sync_open = 1;

        $this->items[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getImage($profilepic, array(
                    'width' => '50',
                    'crop' => 'round',
                    'priority' => '9',
                    'border-width' => '2',
                    'border-color' => '#FFFFFF',
                    'border-radius' => '25'
                )),
            )),
            $this->getColumn(array(
                $this->getText($name, array(
                    'style' => 'desee-menu-name-item',
                )),
            ), array(
                'style' => 'desee-menu-text-column',
            )),
        ), array(
            'padding' => '15 10 15 10',
            'background-color' => '#ff6600',
//            'background-image' => $this->getImageFileName('menu-top.png'),
//            'background-size' => 'cover',
            'vertical-align' => 'middle',
            'onclick' => $onclick,
        ));

        $this->items[] = $this->getSpacer(1, array(
            'margin' => '0 0 0 0',
            'background-color' => '#F5F5F5'
        ));
    }

    public function getNavItems() {
        
        if ( $this->getSavedVariable( 'logged_in' ) ) {
            $nav_items = $this->getMainItems();
        } else {
            $nav_items = $this->getLoggedOutItems();
        }

        foreach ($nav_items as $nav_item) {

            $link = $nav_item['link'];
            $label = $nav_item['label'];

            $tab_id = 'open-action-' . $link;

            if (isset($nav_item['onclick'])) {
                $onclick = $nav_item['onclick'];
            } else {
                $onclick = new stdClass();
                $onclick->id = $tab_id;
                $onclick->action = 'open-action';
                $onclick->action_config = $this->getActionidByPermaname( $link );
                $onclick->config = $this->getActionidByPermaname( $link );
                //$onclick->sync_open = 1;
                if ( isset($nav_item['tab']) ) {
                    $onclick->tab_id = $nav_item['tab'];
                }
            }

            if (isset($nav_item['offset'])) {
                $this->items[] = $this->getSpacer(10);
            }

            if ( !isset($nav_item['disable_icon']) ) {
                $menu_content[] = $this->getImage('ic_menu_' . $link . '.png', array(
                    'padding' => '0 0 0 10',
                    'width' => '35',
                    'vertical-align' => 'middle'
                ));
            }

            $menu_content[] = $this->getText($label, array(
                'font-size' => 15,
                'padding' => '12 8 12 10',
                'color' => '#000000',
            ));

            $this->items[] = $this->getRow($menu_content, array(
                'margin' => '0 0 0 0',
                'background-color' => '#FFFFFF',
                'onclick' => $onclick,
            ));
            $this->items[] = $this->getSpacer(1, array(
                'background-color' => '#F5F5F5'
            ));

            unset( $menu_content );
        }
        if ( empty($nav_items) ) {
            return false;
        }


    }

    public function getLoggedOutItems() {
        return array(
            array(
                'label' => 'Login',
                'link' => 'login',
                'disable_icon' => true,
            ),
            array(
                'label' => 'Register',
                'link' => 'register',
                'disable_icon' => true,
            ),
        );
    }

    public function getMainItems() {

        $onclick = new stdClass();
        $onclick->action = 'share';

        $nav_items = array(
            array(
                'label' => 'Swipe',
                'link' => 'people',
            ),
            array(
                'label' => 'Matches',
                'link' => 'messaging',
            ),
            array(
                'label' => 'Preferences',
                'link' => 'preferences',
            ),
            array(
                'label' => 'Unmatched',
                'link' => 'usersiunmatched'
            ),
            array(
                'label' => 'Invisible List',
                'link' => 'hide'
            ),
            array(
                'label' => 'Extras',
                'link' => 'profileextras',
            ),
            array(
                'label' => 'Invite a Friend',
                'link' => 'invite',
                'onclick' => $onclick
            ),
            array(
                'label' => 'Rate this App',
                'link' => 'rating'
            ),
            array(
                'label' => 'Logout',
                'link' => 'logout',
            )
        );

        if(stristr($this->getSavedVariable('email'),'appzio.com')){
            $nav_items[] = array('label' => 'Debug', 'link' => 'debug');
        }

        if(stristr($this->getSavedVariable('email'),'madhatviking')){
            $nav_items[] = array('label' => 'Debug', 'link' => 'debug');
        }

        $this->metas = new MobilematchingmetaModel();

        if ($this->metas->checkMeta('change-location', $this->playid)) {

            $logout_item = array_pop($nav_items);

            $nav_items[] = array(
                'label' => 'Change Location',
                'link' => 'changelocation'
            );

            $nav_items[]= $logout_item;
        }

        return $nav_items;
    }

}