<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aelogic.article.models.*');
Yii::import('application.modules.aelogic.controllers.*');

class WalletModel extends ArticleModel {

    public $id;
    public $game_id;
    public $gid;
    public $actionid;
    public $debug;
    public $playid;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'ae_ext_wallet';
    }

    public function getWallet( $playid = false ) {

        if ( empty($playid) ) {
            $playid = $this->playid;
        }

        $model = new WalletModel();

        $entry = $model->findByAttributes(array(
            'play_id' => $playid,
        ));

        if ( empty($entry) ) {
            return false;
        }

        return $entry;
    }

    public function createWallet( $promo_funds = false ) {

        $model = new WalletModel();

        $entry = $model->findByAttributes(array(
            'play_id' => $this->playid,
        ));

        if ( $entry ) {
            return false;
        }

        $model->play_id = $this->playid;

        if ( $promo_funds AND is_int( $promo_funds ) ) {
            $model->funds_raw = $promo_funds;
        }

        $model->insert();

        return true;
    }

    public function updateWallet( $sum, $playid = false ) {

        if ( empty($playid) ) {
            $playid = $this->playid;
        }

        $model = new WalletModel();

        $entry = $model->findByAttributes(array(
            'play_id' => $playid,
        ));

        if ( empty($entry) ) {
            return false;
        }

        $entry->funds_raw = $sum;
        $entry->update();

        return true;
    }

}