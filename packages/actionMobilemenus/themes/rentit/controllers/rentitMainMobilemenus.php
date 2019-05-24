<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.article.controllers.*');

class rentitMainMobilemenus extends rentitMobilemenusSubController {

    public $role;
    public $items = array();

    public function tab1(){
        $this->data = new stdClass();

        $this->rewriteActionConfigField( 'background_color', $this->color_topbar );

        $this->role = $this->getVariable('role');
        if ( $subrole = $this->getVariable('subrole') ) {
            $this->role = $subrole;
        }

        $this->getHeading();
        $this->getNavItems();

        $background = $this->getImageFileName('gradient-dark.png');

        $this->data->scroll[] = $this->getColumn($this->items, array(
            'height' => $this->screen_height - 50,
            'background-image' => $background,
            'background-size' => 'cover',
            // 'style' => 'property-menu-shadowbox',
        ));

        return $this->data;
    }

    public function getHeading() {

        $name = '{#anonymous#}';
        $profilepic = $this->getVariable('profilepic') ? $this->getVariable('profilepic') : 'photo-placeholder.jpg';
        
        if ( isset($this->varcontent['first_name']) AND isset($this->varcontent['surname']) ) {
            $name = $this->varcontent['first_name'] . ' ' . $this->varcontent['surname'];
        } else if ( isset($this->varcontent['real_name']) AND !empty($this->varcontent['real_name']) ) {
            $name = $this->varcontent['real_name'];
        }

        $profile_path = 'login';
        if ( $this->getVariable( 'reg_phase' ) AND $this->getVariable( 'reg_phase' ) == 'complete' ) {
            $profile_path = 'myprofile';
        }

        $onclick = new stdClass();
        $onclick->id = 'open-profile';
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getActionidByPermaname( $profile_path );
        $onclick->config = $this->getActionidByPermaname( $profile_path );
        //$onclick->sync_open = 1;

        $role = ( $this->role ? $this->role : '{#user#}' );

        $this->items[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getImage($profilepic, array(
                    'width' => '80',
                    'crop' => 'round',
                    'priority' => '9',
                )),
            )),
            $this->getColumn(array(
                $this->getText($name, array(
                    'style' => 'rentit-menu-name-item',
                )),
                $this->getText( '{#role#}: ' . $role, array(
                    'style' => 'rentit-menu-role-item',
                )),
            ), array(
                'style' => 'rentit-menu-text-column',
            )),
        ), array(
            'padding' => '5 10 5 10',
            'onclick' => $onclick,
        ));

        $this->items[] = $this->getSpacer(1, array(
            'margin' => '15 0 0 0',
            'background-color' => '#434367'
        ));
    }

    public function getNavItems() {

        if ( $this->getSavedVariable( 'logged_in' ) ) {
            $nav_items = $this->getItemsByRole();
        } else {
            $nav_items = $this->getLoggedOutItems();
        }

        if ( empty($nav_items) ) {
            return false;
        }

        foreach ($nav_items as $nav_item) {

            $link = $nav_item['link'];
            $label = $nav_item['label'];

            $tab_id = 'open-action-' . $link;

            $clicker = array();

            $onclick = new stdClass();
            $onclick->id = $tab_id;
            $onclick->action = 'open-action';
            $onclick->action_config = $this->getActionidByPermaname( $link );
            $onclick->config = $this->getActionidByPermaname( $link );

            $clicker[] = $onclick;

            if ($label == 'Filters') {
                $filter = new stdClass();
                $filter->action = 'open-action';
                $filter->action_config = $this->getActionidByPermaname('searchparameters');
                $filter->open_popup = 1;

                $clicker[] = $filter;
            }

            //$onclick->sync_open = 1;
            if ( isset($nav_item['tab']) ) {
                $onclick->tab_id = $nav_item['tab'];
            }

            if (isset($nav_item['offset'])) {
                $this->items[] = $this->getSpacer(40);
                $this->items[] = $this->getSpacer(1, array(
                    'background-color' => '#434367'
                ));
            }

            $this->items[] = $this->getRow(array(
                $this->getText($label, array(
                    'font-size' => 17,
                    'padding' => '9 8 9 10',
                    'color' => '#f2f2f2',
                    'font-ios' => 'Lato-Light',
                    'font-android' => 'Lato-Light'
                )),
            ), array(
                'margin' => '2 0 2 0',
                'onclick' => $clicker,
            ));
            $this->items[] = $this->getSpacer(1, array(
                'background-color' => '#434367'
            ));
        }

    }

    public function getLoggedOutItems() {
        return array(
            array(
                'label' => 'Login',
                'link' => 'login',
            ),
            array(
                'label' => 'Register',
                'link' => 'registration',
            ),
            array(
                'label' => 'Help',
                'link' => 'aboutus',
            ),
        );
    }

    public function getItemsByRole() {

        $nav_items = array();

        switch ( $this->role ) {
            case 'agent':
                $nav_items = array(
                    array(
                        'label' => 'Active Properties',
                        'link' => 'properties',
                    ),
                    array(
                        'label' => 'Inactive Properties',
                        'link' => 'inactive',
                    ),
                    array(
                        'label' => 'Chats',
                        'link' => 'chats',
                    ),
                    array(
                        'label' => 'Help',
                        'link' => 'aboutus',
                        'offset' => true,
                    ),
                    array(
                        'label' => 'Terms & Conditions',
                        'link' => 'termsconditions'
                    ),
                    array(
                        'label' => 'Logout',
                        'link' => 'logout'
                    ),
                );
                break;

            case 'tenant':
                $nav_items = array(
                    array(
                        'label' => 'By Landlords',
                        'link' => 'properties2',
                        'tab' => '2',
                    ),
                    array(
                        'label' => 'By Agents',
                        'link' => 'properties2',
                    ),
                    array(
                        'label' => 'Favourites',
                        'link' => 'properties2',
                        'tab' => '3',
                    ),
                    array(
                        'label' => 'Chats',
                        'link' => 'chats',
                    ),
                    array(
                        'label' => 'Filters',
                        'link' => 'properties2'
                    ),
	                array(
		                'label' => 'Help',
		                'link' => 'aboutus',
		                'offset' => true,
	                ),
                    array(
                        'label' => 'Terms & Conditions',
                        'link' => 'termsconditions'
                    ),
                    array(
                        'label' => 'Logout',
                        'link' => 'logout'
                    ),
                );
                break;

            case 'landlord':
                $nav_items = array(
                    array(
                        'label' => 'Active Properties',
                        'link' => 'properties',
                    ),
                    array(
                        'label' => 'Inactive Properties',
                        'link' => 'inactive',
                    ),
                    array(
                        'label' => 'Matching Tenants',
                        'link' => 'tenants',
                    ),
                    array(
                        'label' => 'Chats',
                        'link' => 'chats',
                    ),
	                array(
		                'label' => 'Management',
		                'link' => 'management',
		                'offset' => true,
	                ),
	                array(
		                'label' => 'Help',
		                'link' => 'aboutus',
	                ),
                    array(
                        'label' => 'Terms & Conditions',
                        'link' => 'termsconditions'
                    ),
	                array(
		                'label' => 'Find Agent',
		                'link' => 'contactagent'
	                ),
                    array(
                        'label' => 'Logout',
                        'link' => 'logout'
                    ),
                );
                break;
        }

        return $nav_items;
    }

}