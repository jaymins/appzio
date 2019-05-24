<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\fitness\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_fit_exercise".
 *
 * @property string $id
 * @property string $app_id
 * @property string $name
 * @property string $category_id
 * @property string $article_id
 * @property integer $points
 * @property integer $duration
 *
 * @property \backend\modules\fitness\models\AeExtCalendarEntry[] $aeExtCalendarEntries
 * @property \backend\modules\fitness\models\AeGame $app
 * @property \backend\modules\fitness\models\AeExtArticle $article
 * @property \backend\modules\fitness\models\AeExtFitProgramCategory $category
 * @property \backend\modules\fitness\models\AeExtFitExerciseComponent[] $aeExtFitExerciseComponents
 * @property \backend\modules\fitness\models\AeExtFitExerciseComponent[] $aeExtFitExerciseComponents0
 * @property \backend\modules\fitness\models\AeExtFitProgramExercise[] $aeExtFitProgramExercises
 * @property string $aliasModel
 */
abstract class AeExtFitExercise extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_fit_exercise';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'name', 'duration'], 'required'],
            [['app_id', 'category_id', 'article_id', 'points', 'duration'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeGame::className(), 'targetAttribute' => ['app_id' => 'id']],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeExtArticle::className(), 'targetAttribute' => ['article_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeExtFitProgramCategory::className(), 'targetAttribute' => ['category_id' => 'id']]
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
            'category_id' => Yii::t('backend', 'Category ID'),
            'article_id' => Yii::t('backend', 'Article ID'),
            'points' => Yii::t('backend', 'Points'),
            'duration' => Yii::t('backend', 'Duration'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtCalendarEntries()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtCalendarEntry::className(), ['exercise_id' => 'id']);
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
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(\backend\modules\fitness\models\AeExtFitProgramCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtFitExerciseComponents()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtFitExerciseComponent::className(), ['exercise_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtFitExerciseComponents0()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtFitExerciseComponent::className(), ['exercise_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtFitProgramExercises()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtFitProgramExercise::className(), ['exercise_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\fitness\models\query\AeExtFitExerciseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\fitness\models\query\AeExtFitExerciseQuery(get_called_class());
    }


}
