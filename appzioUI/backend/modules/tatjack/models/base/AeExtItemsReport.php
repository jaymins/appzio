<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\tatjack\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base-model class for table "ae_ext_items_reports".
 *
 * @property string $id
 * @property string $play_id
 * @property string $item_id
 * @property string $item_owner_id
 * @property string $reason
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \backend\modules\tatjack\models\AeGamePlay $play
 * @property \backend\modules\tatjack\models\AeExtItem $item
 * @property \backend\modules\tatjack\models\AeGamePlay $itemOwner
 * @property string $aliasModel
 */
abstract class AeExtItemsReport extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_items_reports';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['play_id', 'item_id', 'item_owner_id', 'reason'], 'required'],
            [['play_id', 'item_id', 'item_owner_id'], 'integer'],
            [['reason'], 'string'],
            [['play_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\tatjack\models\AeGamePlay::className(), 'targetAttribute' => ['play_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\tatjack\models\AeExtItem::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['item_owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\tatjack\models\AeGamePlay::className(), 'targetAttribute' => ['item_owner_id' => 'id']]
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
            'item_id' => Yii::t('backend', 'Item ID'),
            'item_owner_id' => Yii::t('backend', 'Item Owner ID'),
            'reason' => Yii::t('backend', 'Reason'),
            'created_at' => Yii::t('backend', 'Created At'),
            'updated_at' => Yii::t('backend', 'Updated At'),

            'reporter_id' => Yii::t('backend', 'Reporter ID'),
            'tattoo_owner_id' => Yii::t('backend', 'Tattoo Owner ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlay()
    {
        return $this->hasOne(\backend\modules\tatjack\models\AeGamePlay::className(), ['id' => 'play_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(\backend\modules\tatjack\models\AeExtItem::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemOwner()
    {
        return $this->hasOne(\backend\modules\tatjack\models\AeGamePlay::className(), ['id' => 'item_owner_id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\query\AeExtItemsReportQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\tatjack\models\query\AeExtItemsReportQuery(get_called_class());
    }


}
