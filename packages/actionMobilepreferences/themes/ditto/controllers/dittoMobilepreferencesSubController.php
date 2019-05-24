<?php

Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class dittoMobilepreferencesSubController extends MobilepreferencesController {

    public $storage;

    public $fontstyle = array(
        'width' => '70',
        'color' => '#ffffff',
        'font-style' => 'normal',
        'font-size' => '14',
    );

    public function ditto(){

        $this->storage = new AeplayKeyvaluestorage();

        $statuses = $this->checkUserStatus();
        $allow_updates = $statuses['status'];

        $button_text = '{#update_information#}';

        if (isset($this->menuid) AND $this->menuid == 'update-profile') {
            $button_text = '{#information_updated#}';
            $this->saveVariables();
            $this->loadVariableContent();
        }

        $this->data->scroll[] = $this->getSpacer(10);

        $this->data->scroll[] = $this->getText('{#status#}', array( 'style' => 'preferences-heading' ));

        if ( $allow_updates ) {
            $this->setPreferences();
        } else {
            $this->showPreferencesStatus( $statuses );
        }

        $this->data->scroll[] = $this->getSpacer(15);

        $type = $this->getSavedVariable( 'date_preferences' );
        $share_heading = ( $type == 'acceptor' ? '{#receive_updates_from#}' : '{#share_plans_with#}' );

        $this->data->scroll[] = $this->getText($share_heading . '...', array( 'style' => 'preferences-heading' ));
        // $this->data->scroll[] = $this->getRow(array(
        //         $this->getColumn(array(
        //             $this->getCheckbox('look_for_men', '{#men#}', false, $this->fontstyle)
        //         ), array( 'width' => '50%', 'vertical-align' => 'middle' )),
        //         $this->getColumn(array(
        //             $this->getCheckbox('look_for_women', '{#women#}', false, $this->fontstyle)
        //         ), array( 'width' => '50%', 'vertical-align' => 'middle' )),
        //     ));

        $this->data->scroll[] = $this->formkitCheckbox('look_for_men', '{#men#}', array(
            'type' => 'checkbox',
            'margin' => '0 30 9 0',
        ));

        $this->data->scroll[] = $this->formkitCheckbox('look_for_women', '{#women#}', array(
            'type' => 'checkbox',
            'margin' => '0 30 9 0',
        ));

        $this->data->scroll[] = $this->getText('{#notifications_settings#}', array( 'style' => 'preferences-heading' ));
        $this->data->scroll[] = $this->formkitCheckbox('notify', '{#enable_notifications#}', array(
            'type' => 'checkbox',
            'margin' => '0 30 9 0',
        ));

        $this->data->scroll[] = $this->getSpacer(15);
        
        $this->data->scroll[] = $this->getText('{#activity_distance#}', array( 'style' => 'preferences-heading' ));

        $slider_ball = $this->getImageFileName('slider-ball.png');
        $search_distance = $this->getSavedVariable( 'slider_value_distance' );
        $min_value = '1';
        $max_value = '100';

        $this->data->scroll[] = $this->getRangeslider('', array(
            'variable' => 'slider_value_distance',
            'min_value' => $min_value,
            'max_value' => $max_value,
            'value' => $search_distance,
            'step' => '1',
            'left_track_color' => '#BA5678',
            'right_track_color' => '#AABBCC',
            'thumb_color' => '#00FFFF',
            'track_height' => '4px',
            'thumb_image' => $slider_ball,
            'height' => '35',
            'margin' => '5 15 5 15'
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getText( $min_value . ' KM', array( 'style' => 'slider-values' ) ),
            $this->getText( $search_distance, array( 'variable' => 'slider_value_distance', 'style' => 'slider-values-center' ) ),
            $this->getText( ' KM', array( 'style' => 'slider-values-center-2' ) ),
            $this->getText( $max_value . ' KM', array( 'style' => 'slider-values-right' ) ),
        ), array( 'margin' => '0 30 10 30', ));
        
        $this->data->scroll[] = $this->getSpacer(10);

        $this->data->scroll[] = $this->getText('{#age_range#}', array( 'style' => 'preferences-heading' ));

        $search_age = ( $this->getSavedVariable( 'slider_value_age' ) ? $this->getSavedVariable( 'slider_value_age' ) : '20' );
        $min_value = '18';
        $max_value = '99';

        $this->data->scroll[] = $this->getRangeslider('', array(
            'variable' => 'slider_value_age',
            'min_value' => $min_value,
            'max_value' => $max_value,
            'value' => $search_age,
            'step' => '1',
            'left_track_color' => '#BA5678',
            'right_track_color' => '#AABBCC',
            'thumb_color' => '#00FFFF',
            'track_height' => '4px',
            'thumb_image' => $slider_ball,
            'height' => '35',
            'margin' => '5 15 5 15'
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getText( $min_value, array( 'style' => 'slider-values' ) ),
            $this->getText( $search_age, array( 'variable' => 'slider_value_age', 'style' => 'slider-values-center' ) ),
            $this->getText( $max_value, array( 'style' => 'slider-values-right' ) ),
        ), array( 'margin' => '0 30 10 30', ));

        
        $this->data->scroll[] = $this->getSpacer(10);

        /*
        if ( $type == 'acceptor' ) {
            $params = array_merge( $this->fontstyle, array( 'width' => 230 ) );
            $this->data->scroll[] = $this->getCheckbox('show_cash_only', '{#show_plans_with_cash_bonus_only?#}', false, $params);
        }
        */

        if ( $allow_updates ) {
            $this->data->footer[] = $this->getTextbutton($button_text, array( 'id' => 'update-profile', 'style' => 'submit-button' ));            
        } else {
            $this->data->footer[] = $this->getRow(array(
                $this->getColumn(array(
                    $this->getText($button_text, array( 'style' => 'rwt-heading', )),
                    $this->getRow(array(
                        $this->getText('(Please cancel your current plans first)', array( 'style' => 'listing-heading-text', )),
                    ), array( 'text-align' => 'center' )),
                )),
            ), array( 'style' => 'submit-button-gray' ));
        }
    }

    public function setPreferences() {

        $type = $this->getSavedVariable( 'date_preferences' );
        
        switch ($this->menuid) {
            case 'acceptor':
                $class_acc = 'button-pref-selected';
                $class_req = 'button-pref';
                $this->saveVariable('date_preferences','acceptor');
                $this->resetUserStatus();
                break;
            
            case 'requestor':
                $class_acc = 'button-pref';
                $class_req = 'button-pref-selected';
                $this->saveVariable('date_preferences','requestor');
                $this->saveVariable('date_phase','step-1');
                $this->saveVariable('to_requestor_stamp', time());
                $this->resetUserStatus();
                break;

            default:

                if ( $type == 'acceptor' ) {
                    $class_acc = 'button-pref-selected';
                    $class_req = 'button-pref';
                } else if ( $type == 'requestor' ) {
                    $class_acc = 'button-pref';
                    $class_req = 'button-pref-selected';
                } else {
                    $class_acc = 'button-pref';
                    $class_req = 'button-pref-selected';
                }

                break;
        }

        if ( $class_req == 'button-pref-selected' ) {
            $columns[] = $this->getText('{#planner#}', array( 'style' => $class_req ));
        } else {
            $columns[] = $this->getTextbutton('{#planner#}', array( 'id' => 'requestor', 'style' => $class_req ));
        }

        if ( $class_acc == 'button-pref-selected' ) {
            $columns[] = $this->getText('{#acceptor#}', array( 'style' => $class_acc ));
        } else {
            $columns[] = $this->getTextbutton('{#acceptor#}', array('id' => 'acceptor', 'style' => $class_acc));
        }

        $this->data->scroll[] = $this->getRow($columns, array('style' => 'button-pref-container'));
    }

    public function showPreferencesStatus( $statuses ) {

        $text = '';

        if ( $statuses['user_type'] == 'requestor' ) {
            $vars = $this->getPlayVariables();
            if ( isset($vars['timer']) AND !empty($vars['timer']) ) {
                $text = 'Please cancel your Plan request before changing your Status';
            }
        } else if ( $statuses['user_type'] == 'acceptor' ) {
            if ( $statuses['requested_date'] ) {
                $text = 'Please confirm or cancel or your Plan requests before you could change your Status';
            } else if ( $statuses['date'] ) {
                $user = $statuses['date']['value'];
                $info = $this->getPlayVariables( $user );

                $name = isset($info['name']) ? $info['name'] : 'anonymous';

                $text = 'You have to end your plan with '. ucfirst($name) .' in order to change your current profile status.';
            }
        }

        $this->data->scroll[] = $this->getText($text, array( 'style' => 'preferences-text' ));
    }

    /*
    * Resets the user's status
    * All users have an initial state of "match_always" = 0, meaning that they are not looking for any "plans"
    */
    public function resetUserStatus() {
        $user = MobilematchingModel::model()->findByAttributes( array( 'play_id' => $this->playid ) );
        if(is_object($user)){
            $user->match_always = 0;
            $user->update();
        }
    }

    public function checkUserStatus() {
        $vars = $this->getPlayVariables();
        $user_type = $vars['date_preferences'];

        $status = true;
        $requested_date = '';
        $date = '';

        // Check for active requests
        if ( $user_type == 'requestor' ) {

            // No active requests
            if ( isset($vars['timer']) AND !empty($vars['timer']) ) {
                $status = false;
            } else {
                $status = true;
            }

        } else if ( $user_type == 'acceptor' ) {

            $requested_date = $this->storage->findByAttributes(array(
                'value' => $this->playid,
                'key' => 'requested_match',
            ));

            $date = $this->storage->findByAttributes(array(
                'play_id' => $this->playid,
                'key' => 'twoway_matches',
            ));

            // Active date or requested date - do NOT allow updates
            if ( $date OR $requested_date ) {
                $status = false;
            }
        }

        return array(
            'status' => $status,
            'user_type' => $user_type,
            'requested_date' => $requested_date,
            'date' => $date,
        );
    }

}