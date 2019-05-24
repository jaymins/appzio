<?php

class MobilematchingmetaModel extends CActiveRecord
{

    public $id;
    public $mobilematching_id;
    public $play_id;
    public $meta_key;
    public $meta_value;
    public $meta_limit;

    public $current_playid;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobilematching_meta';
    }

    public function relations()
    {
        return array(
            // 'mobilematching_id' => array(self::BELONGS_TO, 'ae_ext_mobilematching', 'id'),
            'play_id' => array(self::BELONGS_TO, 'ae_game_play', 'id'),
        );
    }

    public static function getMatchingMeta( $play_id ) {
        $items = MobilematchingmetaModel::model()->findAllByAttributes(array(
            'play_id' => $play_id
        ));

        return $items;
    }

    /*
    * Update or Save the Mobilematching meta values when needed
    */
    public function saveMeta() {

        $meta_entry = MobilematchingmetaModel::model()->findByAttributes(array(
            'play_id' => $this->play_id,
            'meta_key' => $this->meta_key,
        ));

        if ( is_object($meta_entry) ) {
            $meta_entry->meta_value = $this->meta_value;
            $meta_entry->update();
            return true;
        }

        $metaObj = new MobilematchingmetaModel();
        $metaObj->play_id = $this->play_id;
        $metaObj->meta_key = $this->meta_key;
        $metaObj->meta_value = $this->meta_value;
        $metaObj->meta_limit = $this->meta_limit;

        if ( $metaObj->save() ) {
            return true;
        }

        return false;
    }

    public function decrementValue( $key ) {

        $metaObj = MobilematchingmetaModel::model()->findByAttributes(array(
            'play_id' => $this->play_id,
            'meta_key' => $key,
        ));

        if ( !is_object($metaObj) ) {
            return false;
        }

        $current_value = $metaObj->meta_value;

        if ( !is_numeric($current_value) ) {
            return false;
        }

        $metaObj->meta_value = (int) $current_value - 1;
        $metaObj->update();

        return true;
    }

    /**
     * Check if a "profile extra" is valid
     * Return "true" if certain meta is valid
     */
    public function checkMeta( $meta_key, $play_id = false ) {

        if ( $play_id ) {
            $this->current_playid = $play_id;
        }

        $metas = MobilematchingmetaModel::model()->getMatchingMeta( $this->current_playid );

        if ( empty($metas) ) {
            return false;
        }

        $time = time();
        $configs = $this->getExtrasCards();

        foreach ($metas as $extra) {

            if ( $extra->meta_key != $meta_key ) {
                continue;
            }

            $card_config = $configs[$meta_key];

            $limit = $extra->meta_limit;
            $value = $extra->meta_value;

            switch ($limit) {
                case 'swipes':

                    if ( $value > 0 ) {
                        return $value;
                    }

                    break;

                case 'time':
                    $seconds_left = $card_config['amount'] - ( $time - $value );

                    if ( $seconds_left > 0 ) {
                        return true;
                    }

                    break;
            }

        }

        return false;
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
                'measurement' => 'time',
                'amount' => '2592000',
                'product_id_ios' => 'unlimited_swipes.01',
                'product_id_android' => 'unlimited_swipes.001',
            ),
            'spark-profile' => array(
                'measurement' => 'time',
                'amount' => '43200', // 12 hours in seconds
                'product_id_ios' => 'spark_profile.01',
                'product_id_android' => 'spark_profile.001',
            ),
            'extra-superhot' => array(
                'measurement' => 'swipes',
                'amount' => '10',
                'product_id_ios' => 'superhot.01',
                'product_id_android' => 'superhot.001',
            ),
            /*'no-ads' => array(
                'measurement' => 'time',
                'amount' => '2592000', // 30 days in seconds
            ),*/
            'hide-distance' => array(
                'measurement' => 'time',
                'amount' => '2592000',
                'product_id_ios' => 'distance_invisible.01',
                'product_id_android' => 'distance_invisible.001',
            ),
            'hide-age' => array(
                'measurement' => 'time',
                'amount' => '2592000',
                'product_id_ios' => 'age_invisible.01',
                'product_id_android' => 'age_invisible.001',
            ),
            'active-users-first' => array(
                'measurement' => 'time',
                'amount' => '2592000',
                'product_id_ios' => 'active_stack.01',
                'product_id_android' => 'active_stack.001',
            ),
            'chat-with-blocked' => array(
                'measurement' => 'time',
                'amount' => '2592000',
                'product_id_ios' => 'second_chance.01',
                'product_id_android' => 'second_chance.001',
            ),
            'send-media' => array(
                'measurement' => 'time',
                'amount' => '2592000',
                'product_id_ios' => 'attach_photos.01',
                'product_id_android' => 'attach_photos.001',
            ),
            'refresh-all-swipes' => array(
                'measurement' => 'time',
                'amount' => '2592000',
                'product_id_ios' => 'refresh_profile.01',
                'product_id_android' => 'refresh_profile.001',
            ),
            'swipe-back' => array(
                'measurement' => 'swipes',
                'amount' => '10',
                'product_id_ios' => 'retrace_swipes.01',
                'product_id_android' => 'retrace_swipes.001',
            ),
            'next-mutual-likes' => array(
                'measurement' => 'time',
                'amount' => '2592000',
                'product_id_ios' => 'match_countdown.01',
                'product_id_android' => 'match_countdown.001',
            ),
            'change-location' => array(
                'measurement' => 'time',
                'amount' => '2592000',
                'product_id_ios' => 'change_location.01',
                'product_id_android' => 'change_location.001',
            )
        );
    }

}