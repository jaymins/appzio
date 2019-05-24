<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\articles\models\base;

use Yii;

/**
 * This is the base-model class for table "ae_ext_article_categories".
 *
 * @property string $id
 * @property string $app_id
 * @property string $parent_id
 * @property integer $sorting
 * @property string $title
 * @property string $headertext
 * @property string $description
 * @property string $picture
 *
 * @property \backend\modules\articles\models\AeExtArticle[] $aeExtArticles
 * @property \backend\modules\articles\models\AeGame $app
 * @property string $aliasModel
 */
abstract class AeExtArticleCategory extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ae_ext_article_categories';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'parent_id', 'sorting', 'title', 'headertext', 'description', 'picture'], 'required'],
            [['app_id', 'parent_id', 'sorting'], 'integer'],
            [['description'], 'string'],
            [['title', 'headertext', 'picture'], 'string', 'max' => 255],
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
            'parent_id' => Yii::t('backend', 'Parent ID'),
            'sorting' => Yii::t('backend', 'Sorting'),
            'title' => Yii::t('backend', 'Title'),
            'headertext' => Yii::t('backend', 'Headertext'),
            'description' => Yii::t('backend', 'Description'),
            'picture' => Yii::t('backend', 'Picture'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAeExtArticles()
    {
        return $this->hasMany(\backend\modules\articles\models\AeExtArticle::className(), ['category_id' => 'id']);
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
     * @return \backend\modules\articles\models\query\AeExtArticleCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\articles\models\query\AeExtArticleCategoryQuery(get_called_class());
    }


}
