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

    'turn_to_recipe' =>
        array('items' => array(
            array('shortname' => 'yes_lets_doit', 'image' => 'letsdoit.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
            array('shortname' => 'not_this_time', 'image' => 'not-this-time.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
        )
        ),


);

?>