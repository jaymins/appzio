<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\registration\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_mregister_companies".
 *
 * @property integer $id
 * @property integer $app_id
 * @property string $name
 * @property integer $subscription_active
 * @property string $subscription_expires
 * @property integer $user_limit
 * @property string $notes
 * @property integer $admit_by_domain
 * @property string $domain
 *
 * @property \backend\modules\registration\models\AeGame $app
 * @property \backend\modules\registration\models\AeExtMregisterCompaniesUser[] $aeExtMregisterCompaniesUsers
 * @property string $aliasModel
 */
abstract class AeExtMregisterCompany extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_mregister_companies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'name'], 'required'],
            [['app_id', 'subscription_active', 'user_limit', 'admit_by_domain'], 'integer'],
            [['subscription_expires'], 'safe'],
            [['notes'], 'string'],
            [['name', 'domain'], 'string', 'max' => 255],
            [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\registration\models\AeGame::className(), 'targetAttribute' => ['app_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'app_id' => Yii::t('backend', 'App ID'),
            'name' => Yii::t('backend', 'Name'),
            'subscription_active' => Yii::t('backend', 'Subscription Active'),
            'subscription_expires' => Yii::t('backend', 'Subscription Expires'),
            'user_limit' => Yii::t('backend', 'User Limit'),
            'notes' => Yii::t('backend', 'Notes'),
            'admit_by_domain' => Yii::t('backend', 'Admit By Doman'),
            'domain' => Yii::t('backend', 'Domain'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApp()
    {
        return $this->hasOne(\backend\modules\registration\models\AeGame::className(), ['id' => 'app_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtMregisterCompaniesUsers()
    {
        return $this->hasMany(\backend\modules\registration\models\AeExtMregisterCompaniesUser::className(), ['company_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\registration\models\query\AeExtMregisterCompanyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\registration\models\query\AeExtMregisterCompanyQuery(get_called_class());
    }


}
