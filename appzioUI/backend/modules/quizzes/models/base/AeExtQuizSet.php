<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\quizzes\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_quiz_sets".
 *
 * @property string $id
 * @property string $app_id
 * @property string $quiz_id
 * @property string $question_id
 * @property integer $sorting
 *
 * @property \backend\modules\quizzes\models\AeGame $app
 * @property \backend\modules\quizzes\models\AeExtQuiz $quiz
 * @property \backend\modules\quizzes\models\AeExtQuizQuestion $question
 * @property string $aliasModel
 */
abstract class AeExtQuizSet extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_quiz_sets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'quiz_id', 'question_id', 'sorting'], 'required'],
            [['app_id', 'quiz_id', 'question_id', 'sorting'], 'integer'],
            [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\quizzes\models\AeGame::className(), 'targetAttribute' => ['app_id' => 'id']],
            [['quiz_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\quizzes\models\AeExtQuiz::className(), 'targetAttribute' => ['quiz_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\quizzes\models\AeExtQuizQuestion::className(), 'targetAttribute' => ['question_id' => 'id']]
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
            'quiz_id' => Yii::t('backend', 'Quiz ID'),
            'question_id' => Yii::t('backend', 'Question ID'),
            'sorting' => Yii::t('backend', 'Sorting'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApp()
    {
        return $this->hasOne(\backend\modules\quizzes\models\AeGame::className(), ['id' => 'app_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuiz()
    {
        return $this->hasOne(\backend\modules\quizzes\models\AeExtQuiz::className(), ['id' => 'quiz_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(\backend\modules\quizzes\models\AeExtQuizQuestion::className(), ['id' => 'question_id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\quizzes\models\query\AeExtQuizSetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\quizzes\models\query\AeExtQuizSetQuery(get_called_class());
    }


}
