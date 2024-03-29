<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\fitness\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_fit_movement".
 *
 * @property string $id
 * @property string $app_id
 * @property string $article_id
 * @property string $name
 * @property string $description
 * @property string $video_url
 *
 * @property \backend\modules\fitness\models\AeExtFitComponentMovement[] $aeExtFitComponentMovements
 * @property \backend\modules\fitness\models\AeGame $app
 * @property \backend\modules\fitness\models\AeExtArticle $article
 * @property string $aliasModel
 */
abstract class AeExtFitMovement extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_fit_movement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'name'], 'required'],
            [['app_id', 'article_id'], 'integer'],
            [['name', 'description', 'video_url'], 'string', 'max' => 255],
            [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeGame::className(), 'targetAttribute' => ['app_id' => 'id']],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeExtArticle::className(), 'targetAttribute' => ['article_id' => 'id']]
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
            'article_id' => Yii::t('backend', 'Article ID'),
            'name' => Yii::t('backend', 'Name'),
            'description' => Yii::t('backend', 'Description'),
            'video_url' => Yii::t('backend', 'Video Url'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtFitComponentMovements()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtFitComponentMovement::className(), ['movement_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApp()
    {
        return $this->hasOne(\backend\modules\fitness\models\AeGame::className(), ['id' => 'app_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(\backend\modules\fitness\models\AeExtArticle::className(), ['id' => 'article_id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\fitness\models\query\AeExtFitMovementQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\fitness\models\query\AeExtFitMovementQuery(get_called_class());
    }


}
