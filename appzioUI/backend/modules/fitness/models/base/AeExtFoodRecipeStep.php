<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\fitness\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_food_recipe_step".
 *
 * @property string $id
 * @property string $recipe_id
 * @property integer $time
 * @property string $description
 *
 * @property \backend\modules\fitness\models\AeExtFoodRecipe $recipe
 * @property string $aliasModel
 */
abstract class AeExtFoodRecipeStep extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_food_recipe_step';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recipe_id', 'description'], 'required'],
            [['recipe_id', 'time'], 'integer'],
            [['description'], 'string'],
            [['recipe_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeExtFoodRecipe::className(), 'targetAttribute' => ['recipe_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'recipe_id' => Yii::t('backend', 'Recipe ID'),
            'time' => Yii::t('backend', 'Time'),
            'description' => Yii::t('backend', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipe()
    {
        return $this->hasOne(\backend\modules\fitness\models\AeExtFoodRecipe::className(), ['id' => 'recipe_id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\fitness\models\query\AeExtFoodRecipeStepQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\fitness\models\query\AeExtFoodRecipeStepQuery(get_called_class());
    }


}
