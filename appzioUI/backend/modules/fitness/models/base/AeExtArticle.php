<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\fitness\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_article".
 *
 * @property string $id
 * @property string $app_id
 * @property string $category_id
 * @property string $play_id
 * @property string $title
 * @property string $header
 * @property string $content
 * @property string $link
 * @property integer $rating
 * @property integer $featured
 * @property string $article_date
 *
 * @property \backend\modules\fitness\models\AeGamePlay $play
 * @property \backend\modules\fitness\models\AeGame $app
 * @property \backend\modules\fitness\models\AeExtArticleCategory $category
 * @property \backend\modules\fitness\models\AeExtArticleBookmark[] $aeExtArticleBookmarks
 * @property \backend\modules\fitness\models\AeExtArticlePhoto[] $aeExtArticlePhotos
 * @property \backend\modules\fitness\models\AeExtFitExercise[] $aeExtFitExercises
 * @property \backend\modules\fitness\models\AeExtFitMovement[] $aeExtFitMovements
 * @property \backend\modules\fitness\models\AeExtFitProgram[] $aeExtFitPrograms
 * @property string $aliasModel
 */
abstract class AeExtArticle extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'title', 'header', 'content', 'link'], 'required'],
            [['app_id', 'category_id', 'play_id', 'rating', 'featured'], 'integer'],
            [['header', 'content', 'link'], 'string'],
            [['article_date'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['play_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeGamePlay::className(), 'targetAttribute' => ['play_id' => 'id']],
            [['app_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeGame::className(), 'targetAttribute' => ['app_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\modules\fitness\models\AeExtArticleCategory::className(), 'targetAttribute' => ['category_id' => 'id']]
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
            'category_id' => Yii::t('backend', 'Category ID'),
            'play_id' => Yii::t('backend', 'Play ID'),
            'title' => Yii::t('backend', 'Title'),
            'header' => Yii::t('backend', 'Header'),
            'content' => Yii::t('backend', 'Content'),
            'link' => Yii::t('backend', 'Link'),
            'rating' => Yii::t('backend', 'Rating'),
            'featured' => Yii::t('backend', 'Featured'),
            'article_date' => Yii::t('backend', 'Article Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlay()
    {
        return $this->hasOne(\backend\modules\fitness\models\AeGamePlay::className(), ['id' => 'play_id']);
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
    public function getCategory()
    {
        return $this->hasOne(\backend\modules\fitness\models\AeExtArticleCategory::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticleBookmarks()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtArticleBookmark::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticlePhotos()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtArticlePhoto::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtFitExercises()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtFitExercise::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtFitMovements()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtFitMovement::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtFitPrograms()
    {
        return $this->hasMany(\backend\modules\fitness\models\AeExtFitProgram::className(), ['article_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\fitness\models\query\AeExtArticleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\fitness\models\query\AeExtArticleQuery(get_called_class());
    }


}
