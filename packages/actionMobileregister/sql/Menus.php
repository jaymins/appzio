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

    'mobilereg_connect_facebook' =>
        array('items' => array(
           array('shortname' => 'mobilereg_connect_facebook', 'image' => 'fbconnect.png', 'action' => 'fb-login','action_config' => '','open_popup' => '0'),
         //  array('shortname' => 'mobilereg_connect_facebook2', 'image' => 'fbconnect.png', 'action' => 'fb-login','action_config' => '','open_popup' => '0')
        )

        ),

    'mobilereg_do_registration' =>
        array('items' => array(
            array('shortname' => 'mobilereg_do_registration', 'image' => 'register.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
           // array('shortname' => 'mobilereg_do_registration2', 'image' => 'fbconnect.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0')
        )

        ),

    'mobilereg_finish_registration' =>
        array('items' => array(
            array('shortname' => 'mobilereg_finish_registration', 'image' => 'register.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
                // array('shortname' => 'mobilereg_do_registration2', 'image' => 'fbconnect.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0')
        )

        ),

    'mobilereg_add_photo' =>
        array('items' => array(
            array('shortname' => 'mobilereg_add_photo', 'image' => 'upload_image.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0'),
                // array('shortname' => 'mobilereg_do_registration2', 'image' => 'fbconnect.png', 'action' => 'submit-form-content','action_config' => '','open_popup' => '0')
        )

        ),


);

?>