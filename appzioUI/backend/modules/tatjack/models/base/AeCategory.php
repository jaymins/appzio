<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\tatjack\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_category".
 *
 * @property string $id
 * @property string $name
 * @property integer $visible
 *
 * @property \backend\modules\tatjack\models\AeGame[] $aeGames
 * @property string $aliasModel
 */
abstract class AeCategory extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_category';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'name' => Yii::t('backend', 'Name'),
            'visible' => Yii::t('backend', 'Visible'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeGames()
    {
        return $this->hasMany(\backend\modules\tatjack\models\AeGame::className(), ['category_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\tatjack\models\query\AeCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\tatjack\models\query\AeCategoryQuery(get_called_class());
    }


}
