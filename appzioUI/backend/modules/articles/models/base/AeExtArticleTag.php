<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\articles\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_article_tags".
 *
 * @property string $id
 * @property string $app_id
 * @property string $title
 *
 * @property \backend\modules\articles\models\AeGame $app
 * @property string $aliasModel
 */
abstract class AeExtArticleTag extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_article_tags';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'title'], 'required'],
            [['app_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\articles\models\AeGame::className(), 'targetAttribute' => ['app_id' => 'id']]
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
            'title' => Yii::t('backend', 'Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApp()
    {
        return $this->hasOne(\backend\modules\articles\models\AeGame::className(), ['id' => 'app_id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\articles\models\query\AeExtArticleTagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\articles\models\query\AeExtArticleTagQuery(get_called_class());
    }


}
