<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\products\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_products_reviews".
 *
 * @property integer $id
 * @property integer $play_id
 * @property integer $rating
 * @property string $comment
 *
 * @property \backend\modules\products\models\AeGamePlay $play
 * @property string $aliasModel
 */
abstract class AeExtProductsReview extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_products_reviews';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['play_id', 'rating', 'comment'], 'required'],
            [['play_id', 'rating'], 'integer'],
            [['comment'], 'string'],
            [['play_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\products\models\AeGamePlay::className(), 'targetAttribute' => ['play_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'play_id' => Yii::t('backend', 'Play ID'),
            'rating' => Yii::t('backend', 'Rating'),
            'comment' => Yii::t('backend', 'Comment'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlay()
    {
        return $this->hasOne(\backend\modules\products\models\AeGamePlay::className(), ['id' => 'play_id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\products\models\query\AeExtProductsReviewQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\products\models\query\AeExtProductsReviewQuery(get_called_class());
    }


}
