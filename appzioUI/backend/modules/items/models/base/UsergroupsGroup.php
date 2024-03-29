<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\items\models\base;

use Yii;

/**
 * This is the base-model class for table "usergroups_group".
 *
 * @property string $id
 * @property string $groupname
 * @property integer $level
 * @property string $home
 *
 * @property \backend\modules\items\models\UsergroupsUser[] $usergroupsUsers
 * @property string $aliasModel
 */
abstract class UsergroupsGroup extends \backend\models\CrudBase
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'usergroups_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groupname'], 'required'],
            [['level'], 'integer'],
            [['groupname', 'home'], 'string', 'max' => 120],
            [['groupname'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'groupname' => Yii::t('backend', 'Groupname'),
            'level' => Yii::t('backend', 'Level'),
            'home' => Yii::t('backend', 'Home'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsergroupsUsers()
    {
        return $this->hasMany(\backend\modules\items\models\UsergroupsUser::className(), ['group_id' => 'id']);
    }


    
    /**
     * @inheritdoc
     * @return \backend\modules\items\models\query\UsergroupsGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\modules\items\models\query\UsergroupsGroupQuery(get_called_class());
    }


}
