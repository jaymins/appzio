<?php

class deseeExtrasMobileuserprofile extends MobileuserprofileDeseeController {

    private $matching_extras;
    private $time;
    private $card_key;
    private $card_config;
    private $matching_id;

    public function tab1(){

        $this->time = time();

        $this->data = new stdClass();

        $this->initMobileMatching();
        $this->handleProductSubmission();

        $this->matching_extras = $this->mobilematchingmetaobj->getMatchingMeta( $this->playid );
        $cards = $this->getExtrasCards();

        $this->setHeader();

        foreach ($cards as $card_key => $card_config) {
            $this->card_key = $card_key;
            $this->card_config = $card_config;
            $this->getCard();
        }

        return $this->data;
    }

    public function setHeader() {

        $toggleSidemenu = new stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $this->data->header[] = $this->getRow(array(
            $this->getImage('ic_menu_new.png', array(
                'width' => '20',
                'onclick' => $toggleSidemenu
            )),
            $this->getText('{#extras#}', array(
                'color' => '#ff6600',
                'width' => '90%',
                'text-align' => 'center',
            ))
        ), array(
            'background-color' => '#FFFFFF',
            'padding' => '10 20 10 20',
            'width' => '100%',
        ));
        $this->data->header[] = $this->getImage('header-shadow.png', array(
            'imgwidth' => '1440',
            'width' => '100%',
        ));

        return true;
    }

    public function getCardByProductID( $product_id ) {
        $cards = $this->getExtrasCards();

        foreach ( $cards as $key => $card ) {
            if ( $card['product_id_ios'] == $product_id OR $card['product_id_android'] == $product_id ) {
                $card['trigger'] = $key;
                return $card;
            }
        }

        return false;
    }

    public function getExtrasCards() {
        return array(
            'unlimited-swipes' => array(
                'title' => '{#unlimited_swipes#}',
                'description' => '{#unlimited_swipes_description#}',
                'icon' => 'm_active_users_first.png',
                'measurement' => 'time',
                'amount' => '2591999', // 30 days in seconds
                'product_id_ios' => 'unlimited_swipes.01',
                'product_id_android' => 'unlimited_swipes.001',
            ),
            'spark-profile' => array(
                'title' => '{#spark_profile#}',
                'description' => '{#spark_profile_description#}',
                'icon' => 'm_spark_profile.png',
                'measurement' => 'time',
                'amount' => '43200', // 12 hours in seconds
                'product_id_ios' => 'spark_profile.01',
                'product_id_android' => 'spark_profile.001',
            ),
            'extra-superhot' => array(
                'title' => '{#extra_superhot#}',
                'description' => '{#extra_superhot_description#}',
                'icon' => 'm_extra_superhot.png',
                'measurement' => 'swipes',
                'amount' => '10',
                'product_id_ios' => 'superhot.01',
                'product_id_android' => 'superhot.001',
            ),
            /*'no-ads' => array(
                'title' => '{#no_ads#}',
                'description' => '{#no_ads_description#}',
                'icon' => 'm_no_ads.png',
                'measurement' => 'time',
                'amount' => '2591999', // 30 days in seconds
            ),*/
            'hide-distance' => array(
                'title' => '{#hide_your_distance#}',
                'description' => '{#hide_your_distance_description#}',
                'icon' => 'm_hide_location.png',
                'measurement' => 'time',
                'amount' => '2591999', // 30 days in seconds
                'product_id_ios' => 'distance_invisible.01',
                'product_id_android' => 'distance_invisible.001',
            ),
            'hide-age' => array(
                'title' => '{#hide_your_age#}',
                'description' => '{#hide_your_age_description#}',
                'icon' => 'm_hide_age.png',
                'measurement' => 'time',
                'amount' => '2591999', // 30 days in seconds
                'product_id_ios' => 'age_invisible.01',
                'product_id_android' => 'age_invisible.001',
            ),
            'active-users-first' => array(
                'title' => '{#show_active_users_first#}',
                'description' => '{#show_active_users_first_description#}',
                'icon' => 'm_active_users_first.png',
                'measurement' => 'time',
                'amount' => '2591999', // 30 days in seconds
                'product_id_ios' => 'active_stack.01',
                'product_id_android' => 'active_stack.001',
            ),
            'chat-with-blocked' => array(
                'title' => '{#chat_with_blocked_users#}',
                'description' => '{#chat_with_blocked_users_description#}',
                'icon' => 'm_chat_with_blocked.png',
                'measurement' => 'time',
                'amount' => '2591999', // 30 days in seconds
                'product_id_ios' => 'second_chance.01',
                'product_id_android' => 'second_chance.001',
            ),
            'send-media' => array(
                'title' => '{#send_photos#}',
                'description' => '{#send_photos_description#}',
                'icon' => 'm_share_media.png',
                'measurement' => 'time',
                'amount' => '2591999', // 30 days in seconds
                'product_id_ios' => 'attach_photos.01',
                'product_id_android' => 'attach_photos.001',
            ),
            'refresh-all-swipes' => array(
                'title' => '{#refresh_all_swipes#}',
                'description' => '{#refresh_all_swipes_description#}',
                'icon' => 'm_refresh_all_swipe.png',
                'measurement' => 'one_time',
                'amount' => '0',
                'product_id_ios' => 'refresh_profile.01',
                'product_id_android' => 'refresh_profile.001',
            ),
            'swipe-back' => array(
                'title' => '{#swipe_back#}',
                'description' => '{#swipe_back_description#}',
                'icon' => 'm_swipe_back.png',
                'measurement' => 'swipes',
                'amount' => '10',
                'product_id_ios' => 'retrace_swipes.01',
                'product_id_android' => 'retrace_swipes.001',
            ),
            'next-mutual-likes' => array(
                'title' => '{#know_next_mutual_likes#}',
                'description' => '{#know_next_mutual_likes_description#}',
                'icon' => 'm_next_mutual_like.png',
                'measurement' => 'time',
                'amount' => '2591999', // 30 days in seconds
                'product_id_ios' => 'match_countdown.01',
                'product_id_android' => 'match_countdown.001',
            ),
            'change-location' => array(
                'title' => '{#change_location#}',
                'description' => '{#change_location_description#}',
                'icon' => 'm_change_location.png',
                'measurement' => 'time',
                'amount' => '2591999', // 30 days in seconds
                'product_id_ios' => 'change_location.01',
                'product_id_android' => 'change_location.001',
            )
        );
    }

    public function getCard() {

        $status = $this->checkCardStatus();

        if ( is_array($status) ) {
            $handler = $this->getHandler( $status );
        } else {
            $handler = $this->getRow(array(
                $this->getText('{#buy_now#}', array(
                    'style' => 'desee-purchase-button',
                    // 'onclick' => $this->getPaymentClicker( 1, 1 ),
                    'onclick' => $this->getOnclick('purchase', false, array(
                        'product_id_ios' => $this->card_config['product_id_ios'],
                        'product_id_android' => $this->card_config['product_id_android'],
                    )),
                )),
            ));
        }

        $this->data->scroll[] = $this->getHairline( '#d3d3d4' );

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getImage($this->card_config['icon']),
            ), array('style' => 'desee-extras-card-left-column')),
            $this->getColumn(array(
                $this->getText($this->card_config['title'], array('style' => 'desee-extras-card-heading')),
                $this->getText($this->card_config['description'], array('style' => 'desee-extras-card-description')),
                $handler
            ), array('style' => 'desee-extras-card-right-column')),
        ), array('style' => 'desee-extras-card'));

        $this->data->scroll[] = $this->getRow(array(
            $this->getHairline( '#d3d3d4' ),
        ), array(
            'background-color' => 'f9fafb',
            'padding' => '0 0 7 0'
        ));

    }

    public function getPaymentClicker( $id_android, $id_ios ) {
        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';
        $onclick->id = 'get-product-' . $this->card_key;
        $onclick->sync_close = true;
        return $onclick;
    }

    public function checkCardStatus() {

        if ( empty($this->matching_extras) ) {
            return true;
        }

        foreach ($this->matching_extras as $extra) {

            if ( $extra->meta_key != $this->card_key ) {
                continue;
            }

            $limit = $extra->meta_limit;
            $value = $extra->meta_value;

            switch ($limit) {
                case 'swipes':

                    if ( $value > 0 ) {
                        return array(
                            'limit' => $limit,
                            'value' => $value,
                        );
                    } else {
                        return false;
                    }

                    break;

                case 'time':
                    $seconds_left = $this->card_config['amount'] - ( $this->time - $value );

                    if ( $seconds_left < 0 ) {
                        return false;
                    } else {
                        return array(
                            'limit' => $limit,
                            'value' => $value,
                        );
                    }

                    break;
            }

        }

        return false;
    }

    public function getHandler( $status ) {

        if ( $status['limit'] == 'swipes' ) {

            return $this->getRow(array(
                $this->getText($status['value'] . ' Swipes Left', array('style' => 'desee-item-purchased-button'))
            ));

        } else {
            $seconds_left = $this->card_config['amount'] - ( $this->time - $status['value'] );

            if ( $seconds_left > 86400 ) {
                return $this->getRow(array(
                    $this->getText(gmdate('d', $seconds_left) . ' Days Left', array('style' => 'desee-item-purchased-button'))
                ));
            } else {
                return $this->getRow(array(
                    $this->getRow(array(
                        $this->getTimer($seconds_left, array(
                            'mode' => 'countdown',
                            'style' => 'desee-item-purchased-timer',
                            'timer_id' => 'timer-' . $this->card_key,
                        )),
                        $this->getText(' left', array('style' => 'desee-item-purchased-timer'))
                    ), array(
                        'style' => 'desee-item-purchased-button',
                    )),
                ));
            }

        }

    }

    public function handleProductSubmission() {

        /*if ( !preg_match('~get-product-~', $this->menuid) ) {
            return false;
        }
        $product_id = str_replace('get-product-', '', $this->menuid);*/

        if ( !isset($_REQUEST['purchase_product_id']) ) {
            return false;
        }

        $product_id = $_REQUEST['purchase_product_id'];
        $card_config = $this->getCardByProductID( $product_id );

        if ( empty($card_config) ) {
            return false;
        }

        $trigger = $card_config['trigger'];

        if ( $trigger == 'spark-profile' ) {
            $this->mobilematchingobj->obj_thisuser->is_boosted = 1;
            $this->mobilematchingobj->obj_thisuser->boosted_timestamp = $this->time;
            $this->mobilematchingobj->obj_thisuser->update();
        }

        if ( $trigger == 'refresh-all-swipes' ) {
            $this->mobilematchingobj->resetMatches( false );
            $this->deleteVariable( 'swiped_back_ids' );
            return true;
        }

        if ( $trigger == 'hide-distance' ) {
            $this->saveVariable( 'profile_location_invisible', 1 );
        }

        if ( $trigger == 'hide-age' ) {
            $this->saveVariable( 'profile_age_invisible', 1 );
        }

        $this->mobilematchingmetaobj->play_id = $this->playid;
        $this->mobilematchingmetaobj->meta_key = $trigger;
        $this->mobilematchingmetaobj->meta_value = ( $card_config['measurement'] == 'time' ? $this->time : $card_config['amount'] );
        $this->mobilematchingmetaobj->meta_limit = $card_config['measurement'];
        $this->mobilematchingmetaobj->saveMeta();

        $refresh_events = array(
            'change-location', 'spark-profile', 'swipe-back', 'extra-superhot', 'next-mutual-likes'
        );

        if ( in_array($trigger, $refresh_events) ) {
            $this->data->onload[] = $this->getOnclick( 'list-branches' );
        }

        return true;
    }

}