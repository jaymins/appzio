<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usergroups_user".
 */
class DbadminQueries extends \yii\db\ActiveRecord
{

    public $query_hash;
    public $query_total;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dbadmin_queries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['results_count'], 'integer'],
            [['hash', 'results_count', 'timestamp'], 'required'],
            [['hash'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hash' => 'Hash',
            'results_count' => 'Results Count',
            'timestamp' => 'Timestamp',
        ];
    }

    public function getCachedTotalCount() {

        $model = new DbadminQueries();

        $result = $model->findOne(array(
            'hash' => $this->query_hash
        ));

        if ( empty($result) ) {
            return false;
        }

        $default_time = 86400;
        $current_time = time();

        $seconds_left = $default_time - ( $current_time - $result->timestamp );

        if ( $seconds_left < 0 ) {
            return false;
        }

        return $result->results_count;
    }

    public function storeTotalCount() {

        $model = new DbadminQueries();
        $model->hash = $this->query_hash;
        $model->results_count = $this->query_total;
        $model->timestamp = time();

        if ( $model->insert() ) {
            return true;
        }

        return false;
    }

}