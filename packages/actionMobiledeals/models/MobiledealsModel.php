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

class MobiledealsModel extends ArticleModel {

    public $app_id;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'ae_ext_deals_logs';
    }

    public function getLogEntry() {

        $model = new MobiledealsModel();

        $timestamp = strtotime( 'today midnight' );
        // $time = date( 'F j, Y, H:i:s', $timestamp );
        $ref = date( 'F j, Y', $timestamp );

        $entry = $model->findByAttributes(array(
            'log_date' => $ref
        ));

        // Case 1 - Missing date log
        if ( empty($entry) ) {
            $model->log_date = $ref;
            $model->app_id = $this->app_id;
            $model->insert();

            $entry_id = $model->id;

            $entry = $model->findByPk( $entry_id );

            return $entry;
        }

        // Case 2 - Current date log
        if ( !$entry->log_data_sent ) {
            return $entry;
        }

        // Case 3 - The today's log has been already sent/cleared
        $timestamp = strtotime( 'tomorrow midnight' );
        $ref = date( 'F j, Y', $timestamp );

        $entry = $model->findByAttributes(array(
            'log_date' => $ref
        ));

        if ( empty($entry) ) {
            $model->log_date = $ref;
            $model->app_id = $this->app_id;
            $model->insert();

            $entry_id = $model->id;

            $entry = $model->findByPk( $entry_id );

            return $entry;
        } else {
            return $entry;
        }
        
    }

    /**
    * This method would get the latest *unsend* log entry batch
    */
    public function getLatestLog( $check_if_sent = true ) {
        $model = new MobiledealsModel();

        $query = array();
        if ( $check_if_sent ) {
            $query = array( 'log_data_sent' => 0 );
        }

        $order = array( 'order' => 'id DESC ');

        $entry = $model->findByAttributes( $query, $order );

        if ( $entry ) {
            return $entry;
        }

        return false;
    }

    /**
    * This method would get the latest *unsend* log entry batch
    */
    public function getLatestNtfLog() {
        $model = new MobiledealsModel();

        $query = array( 'log_data_sent' => 1 );
        $order = array( 'order' => 'id DESC ');

        $entry = $model->findByAttributes( $query, $order );

        if ( $entry ) {
            return $entry;
        }

        return false;
    }

    public function getTodaysLog() {
        $model = new MobiledealsModel();

        $timestamp = strtotime( 'today midnight' );
        $ref = date( 'F j, Y', $timestamp );

        $entry = $model->findByAttributes(array(
            'log_date' => $ref
        ));

        if ( $entry ) {
            return $entry;
        }

        return false;
    }

}