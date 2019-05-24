<?php

/*
 * list-branches
 * list-branch-actions
 * call-backend
 * open-url
 * open-action
 * open-branch
 * open-menu
 * fb-login
 * lotout
 * go-home
 * go-toplist-module
 * go-profile-module
 * go-previews-view
 * share
 * submit-form-content      <--- you would probably mostly using this
 * complete-action
 *
 */


$menus = array(

    'mobileprofile_tabs' =>
        array('items' => array(
            array('shortname' => 'mobileprofile_tab1', 'image' => 'mp1.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
            array('shortname' => 'mobileprofile_tab2', 'image' => 'mp2.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
            array('shortname' => 'mobileprofile_tab3', 'image' => 'mp3.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
            array('shortname' => 'mobileprofile_tab4', 'image' => 'mp4.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
        )
        ),

    'add_photo_button' =>
        array('items' => array(
            array('shortname' => 'add_photo_button', 'image' => 'add-photo-button.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
        )
        ),

    'shoppinglist_top_menu' =>
        array('items' => array(
            array('shortname' => 'shoppinglist_1', 'image' => 'shoppinglist-menu-1.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
            array('shortname' => 'shoppinglist_2', 'image' => 'shoppinglist-menu-2.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
            array('shortname' => 'shoppinglist_3', 'image' => 'shoppinglist-menu-3.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
        )
        ),

    'bookmarks_top_menu' =>
        array('items' => array(
            array('shortname' => 'bookmarks_menu_1', 'image' => 'recipe-bookmarks-menu-1.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
            array('shortname' => 'bookmarks_menu_2', 'image' => 'recipe-bookmarks-menu-2.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
        )
        )


);

?>