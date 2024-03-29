<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\fitness\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_food_ingredient".
 *
 * @property integer $id
 * @property string $name
 * @property string $unit
 * @property string $category_id
 *
 * @property \backend\modules\fitness\models\AeExtFoodIngredientCategory $category
 * @property \backend\modules\fitness\models\AeExtFoodRecipeIngredient[] $aeExtFoodRecipeIngredients
 * @property string $aliasModel
 */
abstract class AeExtFoodIngredient extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_food_ingredient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'category_id'], 'required'],
            [['category_id'], 'integer'],
            [['name', 'unit'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeExtFoodIngredientCategory::className(), 'targetAttribute' => ['category_id' => 'id']]
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
            'unit' => Yii::t('backend', 'Unit'),
            'category_id' => Yii::t('backend', 'Category ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(\backend\modules\fitness\models\AeExtFoodIngredientCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtFoodRecipeIngredients()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtFoodRecipeIngredient::className(), ['ingredient_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\fitness\models\query\AeExtFoodIngredientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\fitness\models\query\AeExtFoodIngredientQuery(get_called_class());
    }


}
